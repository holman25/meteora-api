<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Http;
use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\assertDatabaseHas;

it('stores user message, calls weather and returns assistant', function () {
    $chat = Chat::factory()->create();

    // Fake Open-Meteo (ajusta si usas otro proveedor)
    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'latitude' => 4.711,
            'longitude' => -74.0721,
            'timezone' => 'America/Bogota',
            'current' => ['temperature_2m' => 13.6, 'cloud_cover' => 90, 'wind_speed_10m' => 8],
            'hourly' => ['temperature_2m' => [13,14,15], 'precipitation_probability' => [10,20,30]],
        ], 200),
        '*' => Http::response([], 200),
    ]);

    // Como OPENAI_MOCK=true, el servicio responderá con simulación (si lo tienes activo)
    $resp = postJson("/api/v1/chats/{$chat->id}/messages", [
        'content' => '¿Lloverá en Bogotá mañana?',
        'location' => ['lat' => 4.711, 'lon' => -74.0721],
        'date' => 'mañana',
    ])->assertCreated();

    // Verifica estructura
    $resp->assertJson(fn ($j) =>
        $j->has('user.id')
          ->where('user.chat_id', $chat->id)
          ->where('user.role', 'user')
          ->has('assistant.id')
          ->where('assistant.chat_id', $chat->id)
          ->where('assistant.role', 'assistant')
    );

    // Persiste en DB
    $userId = $resp->json('user.id');
    $assistantId = $resp->json('assistant.id');

    assertDatabaseHas('messages', ['id' => $userId, 'role' => 'user', 'chat_id' => $chat->id]);
    assertDatabaseHas('messages', ['id' => $assistantId, 'role' => 'assistant', 'chat_id' => $chat->id]);

    // Historial accesible (el endpoint devuelve un array plano)
    getJson("/api/v1/chats/{$chat->id}/messages")
        ->assertOk()
        ->assertJsonIsArray(); // ← reemplaza whereType('*','array')
});
