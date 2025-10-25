<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::ulid(),
            'chat_id' => Chat::factory(),
            'role' => 'user',
            'content' => $this->faker->sentence(),
            'model' => null,
            'status' => 'ok',
            'error_code' => null,
            'metadata' => [],
        ];
    }
}
