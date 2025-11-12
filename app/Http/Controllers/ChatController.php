<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Exceptions\TransporterException;
use OpenAI\Exceptions\RateLimitException;

class ChatController extends Controller
{
    public function responder(Request $request)
    {
        try {
            // 🔹 Validar entrada
            $request->validate([
                'pregunta' => 'required|string|min:2',
            ]);

            // 🔹 Registrar en el log
            Log::info('ChatController -> Pregunta recibida', ['pregunta' => $request->pregunta]);

            // 🔹 Esperar 2 segundos para no saturar la API (anti-rate limit)
            sleep(2);

            // 🔹 Crear cliente OpenAI
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            // 🔹 Enviar solicitud al modelo
            $response = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un asistente amigable y experto en redes Cisco.'],
                    ['role' => 'user', 'content' => $request->pregunta],
                ],
            ]);

            // 🔹 Extraer respuesta
            $respuesta = $response->choices[0]->message->content ?? 'No se pudo obtener respuesta.';

            // 🔹 Registrar en el log
            Log::info('ChatController -> Respuesta generada', ['respuesta' => $respuesta]);

            // 🔹 Responder al frontend
            return response()->json([
                'ok' => true,
                'respuesta' => $respuesta,
            ]);

        } catch (RateLimitException $e) {
            Log::warning('CHAT_ERROR_RATELIMIT', ['msg' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'message' => 'Has superado el límite de solicitudes de OpenAI. Espera unos segundos e inténtalo de nuevo.',
            ], 429);

        } catch (TransporterException $e) {
            Log::error('CHAT_ERROR_TRANSPORT', ['msg' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'message' => 'Error de conexión con OpenAI. Revisa tu conexión a internet.',
            ], 500);

        } catch (ErrorException $e) {
            Log::error('CHAT_ERROR_OPENAI', ['msg' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'message' => 'Error en la API de OpenAI. Verifica tu clave o vuelve a intentar.',
            ], 500);

        } catch (\Throwable $e) {
            Log::error('CHAT_ERROR_GENERAL', [
                'type' => get_class($e),
                'msg' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error inesperado en el servidor.',
            ], 500);
        }
    }
}
