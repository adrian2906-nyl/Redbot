<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function responder(Request $request)
    {
        try {
            // 1) Validación fuerte
            $validated = $request->validate([
                'pregunta' => ['required','string','min:2','max:5000'],
            ]);
            $q = mb_strtolower(trim($validated['pregunta']));

            // 2) Filtro temático rápido
            $temas = ['red','vlan','switch','router','ip','subred','firewall','ospf','eigrp','acl','nat','trunk','stp','etherchannel','hsrp','bgp','dhcp','vrrp','mpls','qos'];
            $esTema = false;
            foreach ($temas as $t) { if (str_contains($q, $t)) { $esTema = true; break; } }
            if (!$esTema) {
                return response()->json([
                    'respuesta' => 'Lo siento, ese tema no es de mi área. Solo respondo sobre redes e infraestructura.',
                ], 200);
            }

            // 3) System prompt (instrucciones estrictas)
            $system = <<<'SYS'
Eres un asistente experto en redes Cisco (IOS/IOS-XE).
Debes devolver SIEMPRE un JSON con esta forma EXACTA:
{
  "intent": "cadena-corta",
  "confidence": 0.0,
  "commands": "bloque de comandos IOS",
  "explanation": "explicación humana breve"
}
Reglas:
- "commands" debe ser COPY/PASTE (incluye 'configure terminal' y 'end' si aplica).
- Si falta info, usa valores típicos y dilo en "explanation".
- No devuelvas markdown, ni texto adicional fuera del JSON.
SYS;

            $userMsg = "Usuario: {$q}\nDevolver solo el JSON descrito.";

            // 4) Cliente OpenAI (usa tu binding o fallback)
            // Recomendado: tener el binding 'openai' (openai-php/laravel). Sino, usa el fallback.
            $client = app()->bound('openai')
                ? app('openai')
                : \OpenAI::client(env('OPENAI_API_KEY'));

            // 5) Payload (forzando JSON)
            $payload = [
                'model' => 'gpt-4o-mini',   // ajusta el modelo si quieres
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user',   'content' => $userMsg],
                ],
                'temperature' => 0.2,
                // Forzar JSON en modelos modernos
                'response_format' => ['type' => 'json_object'],
                'max_tokens' => 700, // para evitar respuestas excesivas
            ];

            // 6) Reintentos con backoff
            $attempts = 0; $max = 3; $resp = null;
            while ($attempts < $max) {
                try {
                    $resp = $client->chat()->create($payload);
                    break;
                } catch (\Throwable $e) {
                    $code = (int) ($e->getCode() ?: 0);
                    $attempts++;
                    // Reintenta en 429/5xx
                    if (in_array($code, [429,500,502,503,504,524,529], true) && $attempts < $max) {
                        usleep((int) (pow(2, $attempts) * 250_000)); // 250ms, 500ms, 1s
                        continue;
                    }
                    if ($code === 429) {
                        return response()->json([
                            'message' => 'Rate limit de OpenAI',
                            'detail'  => app()->isLocal() ? $e->getMessage() : null,
                            'retry'   => true,
                        ], 429);
                    }
                    throw $e;
                }
            }

            // 7) Extraer contenido y aislar el JSON robustamente
            $raw = $resp->choices[0]->message->content ?? '{}';
            $json = $this->extractJson($raw);

            if (!$this->isValidSchema($json)) {
                // Log útil para depurar
                Log::warning('CHAT_FORMATO_INVALIDO', ['raw' => $raw, 'parsed' => $json]);
                return response()->json([
                    'error'   => 'FORMATO_INVALIDO',
                    'raw'     => app()->isLocal() ? $raw : null,
                    'message' => 'La IA no devolvió JSON válido con el esquema esperado. Reformula la petición.',
                ], 422);
            }

            // 8) Saneo y recorte de campos (defense-in-depth)
            $out = [
                'intent'      => (string) $json['intent'],
                'confidence'  => max(0.0, min(1.0, (float) $json['confidence'])),
                'commands'    => $this->normalizeCommands((string) $json['commands']),
                'explanation' => $this->sanitizeLine((string) $json['explanation']),
            ];

            return response()->json($out, 200);

        } catch (\Throwable $e) {
            Log::error('CHAT_ERROR', [
                'msg' => $e->getMessage(),
                'trace' => app()->isLocal() ? $e->getTraceAsString() : null,
            ]);
            return response()->json([
                'message' => 'Error interno',
                'detail'  => app()->isLocal() ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Extrae el primer objeto JSON de un texto (soporta ```json ... ``` y ruido).
     */
    private function extractJson(string $raw): array
    {
        $txt = trim($raw);

        // quita fences de markdown
        $txt = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $txt) ?? $txt;

        // intenta decode directo
        $decoded = json_decode($txt, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // busca el primer bloque {...} balanceado de forma simple
        if (preg_match('/\{.*\}/sU', $txt, $m)) {
            $decoded = json_decode($m[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Verifica que tenga las claves esperadas y tipos básicos.
     */
    private function isValidSchema(?array $j): bool
    {
        if (!is_array($j)) return false;
        $req = ['intent','confidence','commands','explanation'];
        foreach ($req as $k) {
            if (!array_key_exists($k, $j)) return false;
        }
        if (!is_string($j['intent'])) return false;
        if (!is_numeric($j['confidence'])) return false;
        if (!is_string($j['commands'])) return false;
        if (!is_string($j['explanation'])) return false;
        return true;
    }

    /**
     * Normaliza comandos: recorta, quita BOM/CRLF raros y limita tamaño.
     */
    private function normalizeCommands(string $s): string
    {
        // línea por línea, trim derecho
        $s = str_replace(["\r\n", "\r"], "\n", $s);
        $lines = array_map(static fn($l) => rtrim($l), explode("\n", $s));
        $s = implode("\n", $lines);
        $s = trim($s);

        // límite duro para evitar payloads enormes
        $max = 8000; // chars
        if (mb_strlen($s) > $max) {
            $s = mb_substr($s, 0, $max) . "\n! [truncado]";
        }
        return $s;
    }

    /**
     * Sanea una línea corta (explicación).
     */
    private function sanitizeLine(string $s): string
    {
        // quita saltos múltiples y espacio excesivo
        $s = preg_replace('/\s+/', ' ', $s) ?? $s;
        $s = trim($s);

        // límite razonable para UI
        $max = 600;
        if (mb_strlen($s) > $max) {
            $s = mb_substr($s, 0, $max) . '…';
        }
        return $s;
    }
}
