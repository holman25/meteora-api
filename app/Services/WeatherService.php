<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class WeatherService
{
    public function forecast(float $lat, float $lon): array
    {
        $start = microtime(true);
        $ttlMinutes = (int) env('OPEN_METEO_TTL_MINUTES', 10);

        // Calculamos info de cache para calcular expiración, esto con el fin de si el usuario envia
        // varias consultas iguales, saber si es cache o no y cuanto tiempo le queda
        $keyBase   = sprintf('forecast:%s:%s', round($lat, 3), round($lon, 3));
        $keyData   = $keyBase.':data';
        $keyExpiry = $keyBase.':expires_at';

        if (Cache::has($keyData)) {
            $data = Cache::get($keyData);
            $expiresAt = Cache::get($keyExpiry);
            $expiresIn = null;

            if ($expiresAt instanceof Carbon) {
                $expiresIn = max(0, $expiresAt->diffInSeconds(now(), false) * -1);
            }

            return [
                'ok'               => true,
                'status'           => 200,
                'data'             => $data,
                'latency_ms'       => (int) ((microtime(true) - $start) * 1000), // acceso local
                'cached'           => true,
                'cache_expires_in' => $expiresIn,
            ];
        }

        try {
            $baseUrl = config('services.open_meteo.base', 'https://api.open-meteo.com/v1');

            $response = Http::timeout(10)->get("{$baseUrl}/forecast", [
                'latitude'   => $lat,
                'longitude'  => $lon,
                'current'    => 'temperature_2m,precipitation,weathercode,cloud_cover,wind_speed_10m,wind_direction_10m',
                'hourly'     => 'temperature_2m,precipitation_probability',
                'daily'      => 'temperature_2m_max,temperature_2m_min,precipitation_sum,wind_speed_10m_max',
                'timezone'   => 'auto',
            ]);

            $latency = (int) ((microtime(true) - $start) * 1000);

            if (!$response->ok()) {
                return [
                    'ok'         => false,
                    'status'     => $response->status(),
                    'data'       => $response->json(),
                    'latency_ms' => $latency,
                    'cached'     => false,
                ];
            }

            $payload   = $response->json();
            $expiresAt = now()->addMinutes($ttlMinutes);

            Cache::put($keyData, $payload, $expiresAt);
            Cache::put($keyExpiry, $expiresAt, $expiresAt);

            return [
                'ok'               => true,
                'status'           => 200,
                'data'             => $payload,
                'latency_ms'       => $latency,
                'cached'           => false,
                'cache_expires_in' => $ttlMinutes * 60,
            ];
        } catch (\Throwable $e) {
            return [
                'ok'         => false,
                'status'     => 500,
                'data'       => ['message' => $e->getMessage()],
                'latency_ms' => (int) ((microtime(true) - $start) * 1000),
                'cached'     => false,
            ];
        }
    }
}
