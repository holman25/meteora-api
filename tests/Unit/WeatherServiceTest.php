<?php

use App\Services\WeatherService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('caches weather responses', function () {
    Cache::flush();

    Http::fake([
        'api.open-meteo.com/*' => Http::sequence()
            ->push(['latitude' => 1, 'longitude' => 2, 'elevation' => 10], 200)
            ->push(['latitude' => 999], 200),
    ]);

    /** @var WeatherService $svc */
    $svc = app(WeatherService::class);

    $a = $svc->forecast(1, 2);
    expect($a['ok'])->toBeTrue()->and($a['cached'] ?? false)->toBeFalse();

    $b = $svc->forecast(1, 2);
    expect($b['ok'])->toBeTrue()->and($b['cached'] ?? false)->toBeTrue();
});
