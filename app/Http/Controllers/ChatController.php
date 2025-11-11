<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Exceptions\TransporterException;

class ChatController extends Controller
{
    public function responder(Request $request)
    {
        try {
            $request->validate(['pregunta' => 'required|string|min:2']);
            $q = mb_strtolower($request->input('pregunta'));

            $temas = ['red','vlan','switch','router','ip','subred','firewall','ospf','eigrp','acl','nat','trunk','stp','etherchannel','hsrp','bgp','dhcp','vrrp','mpls','qos'];
            $esTema = collect($temas)->first(fn($t) => str_contains($q, $t)) !== null;
            if (!$esTema) {
                return response()->json([
                    'respuesta' => 'Lo siento, ese tema no es de mi área. Solo respondo sobre redes e infraestructura.'
                ], 200);
            }

            $system = <<<'SYS'
Eres un asistente experto en redes Cisco (IOS/IOS-XE).
Devuelve SIEMPRE un JSON válido con esta forma EXACTA:
{
  "intent": "cadena-corta",
  "confidence": 0-1,
  "commands": "bloque de comandos IOS",
  "explanation": "explicación humana breve"
}
Reglas:
- "commands" debe ser COPY/PASTE (incluye 'configure terminal' y 'end' si aplica).
- Si falta info, usa valores típicos, pero dilo en "explanation".
- SIN TEXTO extra, SIN markdown, SOLO el JSON.
SYS;

            $userMsg = "Usuario: {$q}\nDevolver solo el JSON descrito.";
            $payload = [
                'model' => 'gpt-4o-mini', // cámbialo si este modelo te limita
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user',   'content' => $userMsg],
                ],
                'temperature' => 0.2,
            ];

            $client = app('openai');

            // --- reintentos con backoff exponencial ---
            $attempts = 0; $max = 3; $resp = null;
            while ($attempts < $max) {
                try {
                    $resp = $client->chat()->create($payload);
                    break; // éxito
                } catch (ErrorException|TransporterException $e) {
                    $code = (int) $e->getCode();
                    // 429 (rate), 5xx: reintentar
                    if (in_array($code, [429, 500, 502, 503, 504, 524, 529], true) && ++$attempts < $max) {
                        usleep((int) (pow(2, $attempts) * 250_000)); // 250ms, 500ms, 1s
                        continue;
                    }
                    // si es 429 y ya no reintentamos, devolver 429 al cliente
                    if ($code === 429) {
                        return response()->json([
                            'message' => 'Rate limit de OpenAI',
                            'detail'  => $e->getMessage(),
                            'retry'   => true
                        ], 429);
                    }
                    throw $e; // otros errores
                }
            }

            $raw = $resp->choices[0]->message->content ?? '{}';
            $json = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($json['commands'])) {
                return response()->json([
                    'error'   => 'FORMATO_INVALIDO',
                    'raw'     => $raw,
                    'message' => 'La IA no devolvió JSON válido. Reformula la petición.'
                ], 422);
            }

            return response()->json($json, 200);

        } catch (\Throwable $e) {
            Log::error('CHAT_ERROR', ['msg' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error interno',
                'detail'  => app()->isLocal() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
