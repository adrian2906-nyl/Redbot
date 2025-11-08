<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;   // <— usa el facade
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function responder(Request $request)
    {
        try {
            // 1) Validación mínima
            $request->validate([
                'pregunta' => 'required|string|min:2',
            ]);

            $q = mb_strtolower($request->input('pregunta'));

            // 2) Filtro de dominio (solo redes / Cisco)
            $temas = [
                'red','vlan','switch','router','ip','subred','firewall',
                'ospf','eigrp','acl','nat','trunk','stp','etherchannel',
                'hsrp','bgp','dhcp','vrrp','mpls','qos'
            ];
            $esTema = false;
            foreach ($temas as $t) {
                if (str_contains($q, $t)) { $esTema = true; break; }
            }

            if (!$esTema) {
                return response()->json([
                    'respuesta' => 'Lo siento, ese tema no es de mi área. Solo respondo sobre redes e infraestructura.'
                ], 200);
            }

            // 3) Prompt del sistema (pide SOLO JSON)
            $system = <<<SYS
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

            // 4) Llamada al modelo (ajusta el id si quieres usar otro)
            $resp = OpenAI::client(env('OPENAI_API_KEY'))
                ->chat()
                ->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user',   'content' => $userMsg],
                    ],
                    'temperature' => 0.2,
                ]);

            $raw = $resp->choices[0]->message->content ?? '{}';

            // 5) Validar que realmente sea JSON con "commands"
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
            Log::error('CHAT_ERROR', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Error interno'], 500);
        }
    }
}
