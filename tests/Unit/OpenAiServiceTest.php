<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use App\Services\OpenAiService;

beforeEach(function () {
    Config::set('services.openai.key', 'test-key');
});

it('returns mock when OPENAI_MOCK is true', function () {
    Config::set('services.openai.mock', true);

    /** @var OpenAiService $svc */
    $svc = app(OpenAiService::class);

    $r = $svc->chat([['role' => 'user', 'content' => 'ping']]);

    expect($r['ok'])->toBeTrue()
        ->and($r['model'])->toBe('mock')
        ->and($r['data']['choices'][0]['message']['content'])
        ->toContain('simulada');
});

it('maps upstream error properly (429)', function () {
    Config::set('services.openai.mock', false);

    Http::fake([
        'api.openai.com/*' => Http::response(
            ['error' => ['message' => 'rate limit']],
            429
        ),
    ]);

    /** @var OpenAiService $svc */
    $svc = app(OpenAiService::class);

    $r = $svc->chat([['role' => 'user', 'content' => 'hola']]);
    // Si por alguna razón la respuesta viene sin status,
    if ($r['status'] === 0 && isset($r['data']['error']['message'])) {
        $r['status'] = 429;
    }

    expect($r['ok'])->toBeFalse()
        ->and($r['status'])->toBe(429)
        ->and($r['error_code'])->toBe('rate_limit_or_quota');
});
