<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use function Pest\Laravel\getJson;

beforeEach(function () {
    Config::set('services.openweather.key', 'test-key');
});

it('returns weather ping with caching fields', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'latitude'  => 4.875,
            'longitude' => -74.25,
            'current'   => ['temperature_2m' => 295.1],
        ], 200),
        'api.openweathermap.org/*' => Http::response([
            'main'    => ['temp' => 295.1],
            'weather' => [['id' => 800]],
        ], 200),
        '*' => Http::response([], 200),
    ]);

    getJson('/api/v1/weather/ping?lat=4.875&lon=-74.25')
        ->assertOk()
        ->assertJson(fn ($j) =>
            $j->where('ok', true)
              ->where('status', 200)
              ->has('latency_ms')
              ->has('cached')
              ->etc() // ← permite 'error' u otras props extra
        );
});
