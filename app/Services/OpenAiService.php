<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class OpenAiService
{
    public function chat(array $messages, ?string $model = null): array
    {
        if (config('services.openai.mock', false)) {
            return [
                'ok'         => true,
                'status'     => 200,
                'data'       => ['choices' => [['message' => ['content' => '[IA MOCK] Respuesta simulada.']]]],
                'latency_ms' => 1,
                'model'      => 'mock',
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
                ->retry($retries, $sleep, fn($e) => $e instanceof ConnectionException)
                ->withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'    => $model,
                    'messages' => $messages,
                ])
                ->throw();

            return [
                'ok'         => true,
                'status'     => $res->status(),
                'data'       => $res->json(),
                'latency_ms' => (int) ((microtime(true) - $start) * 1000),
                'model'      => $model,
            ];
        } catch (RequestException $e) {
            $resp   = $e->response;
            $status = optional($resp)->status() ?? 500;

            return [
                'ok'         => false,
                'status'     => $status,
                'data'       => optional($resp)->json(),
                'latency_ms' => (int) ((microtime(true) - $start) * 1000),
                'model'      => $model,
                'error'      => ['message' => optional($resp)->json('error.message') ?? $e->getMessage()],
                'error_code' => $status === 429 ? 'rate_limit_or_quota'
                    : ($status === 401 ? 'unauthorized' : 'upstream_error'),
                'retry_after' => optional($resp)->header('Retry-After'),
            ];
        } catch (\Throwable $e) {
            return [
                'ok'         => false,
                'status'     => 0,
                'data'       => null,
                'latency_ms' => (int) ((microtime(true) - $start) * 1000),
                'model'      => $model,
                'error'      => ['message' => $e->getMessage()],
                'error_code' => $e instanceof ConnectionException ? 'timeout' : 'network_error',
                'retry_after' => null,
            ];
        }
    }
}
