<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Services\OpenAiService;
use App\Services\WeatherService;
use Illuminate\Support\Arr;

Route::prefix('v1')->group(function () {
    // Públicas
    Route::get('/health', [HealthController::class, 'index']);

    // Protegidas por API Key
    Route::middleware('api.key')->group(function () {
        Route::get('/ai/ping', function (OpenAiService $ai) {
            $resp = $ai->chat([
                ['role' => 'system', 'content' => 'Eres conciso. Responde una palabra.'],
                ['role' => 'user', 'content' => 'Di "ok".']
            ]);

            if (!$resp['ok']) {
                return response()->json([
                    'ok' => false,
                    'status' => $resp['status'],
                    'error_code' => $resp['error_code'],
                    'hint' => $resp['error_code'] === 'rate_limit_or_quota'
                        ? 'Tu cuenta alcanzó el límite/cuota. Revisa billing o usa MOCK.'
                        : 'Verifica modelo/clave o disponibilidad del proveedor.',
                ], 200);
            }

            return response()->json([
                'ok' => true,
                'status' => $resp['status'],
                'model' => $resp['model'],
                'latency_ms' => $resp['latency_ms'],
                'preview' => substr((string) data_get($resp, 'data.choices.0.message.content', ''), 0, 200),
            ]);
        });

        Route::get('/weather/ping', function (WeatherService $wx) {
            $lat = (float) request('lat', 4.711);
            $lon = (float) request('lon', -74.0721);

            $resp = $wx->forecast($lat, $lon);

            return response()->json([
                'ok'         => $resp['ok'],
                'status'     => $resp['status'],
                'latency_ms' => $resp['latency_ms'],
                'cached'     => Arr::get($resp, 'cached', null),
                'cache_expires_in' => Arr::get($resp, 'cache_expires_in', null),
                'summary' => [
                    'latitude'  => data_get($resp, 'data.latitude'),
                    'longitude' => data_get($resp, 'data.longitude'),
                    'elevation' => data_get($resp, 'data.elevation'),
                ],
                'error' => $resp['ok'] ? null : $resp['data'],
            ]);
        });

        // 💬 Chat & mensajes
        Route::post('/chats', [ChatController::class, 'store']);
        Route::post('/chats/{chat}/messages', [MessageController::class, 'store']);
        Route::get('/chats/{chat}/messages', [MessageController::class, 'index']);
        Route::post('/messages/{message}/retry', [MessageController::class, 'retry']);
        Route::get('/chats', [ChatController::class, 'index']);
    });
});
