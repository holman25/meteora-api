<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class OpenAiService
{
    public function chat(array $messages, ?string $model = null): array
    {
        if (config('services.openai.mock', false)) {
            return [
                'ok' => true,
                'status' => 200,
                'data' => ['choices' => [['message' => ['content' => '[IA MOCK] Respuesta simulada.']]]],
                'latency_ms' => 1,
                'model' => 'mock',
            ];
        }

        $apiKey  = config('services.openai.key');
        $model   = $model ?? config('services.openai.model', 'gpt-5-nano');
        $timeout = (int) config('services.openai.timeout', 20);
        $retries = (int) config('services.openai.retries', 2);
        $sleep   = (int) config('services.openai.retry_sleep', 800);

        $start = microtime(true);

        try {
            $res = Http::timeout($timeout)
                ->retry($retries, $sleep, function ($exception, $request) {
                    $response = method_exists($exception, 'response') ? $exception->response : null;
                    $status = $response ? $response->status() : null;
                    // reintentar en timeout/red, 429 y 5xx
                    return $exception instanceof ConnectionException
                        || is_null($status)
                        || $status === 429
                        || ($status >= 500 && $status < 600);
                })
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'    => $model,
                    'messages' => $messages,
                ]);

            $ok = $res->ok();
            $status = $res->status();

            $error = null;
            $errorCode = null;
            if (!$ok) {
                $error = $res->json('error') ?? $res->json();
                $errorCode = match ($status) {
                    401 => 'unauthorized',
                    403 => 'forbidden',
                    404 => 'model_not_found',
                    429 => 'rate_limit_or_quota',
                    default => 'upstream_error',
                };
            }

            return [
                'ok'         => $ok,
                'status'     => $status,
                'data'       => $res->json(),
                'latency_ms' => (int)((microtime(true) - $start) * 1000),
                'model'      => $model,
                'error'      => $error,
                'error_code' => $errorCode,
                'retry_after'=> $res->header('Retry-After'),
            ];
        } catch (\Throwable $e) {
            // timeouts u otros errores de red
            return [
                'ok'         => false,
                'status'     => 0,
                'data'       => null,
                'latency_ms' => (int)((microtime(true) - $start) * 1000),
                'model'      => $model,
                'error'      => ['message' => $e->getMessage()],
                'error_code' => $e instanceof ConnectionException ? 'timeout' : 'network_error',
                'retry_after'=> null,
            ];
        }
    }
}
