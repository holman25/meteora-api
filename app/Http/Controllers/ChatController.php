<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function store(): JsonResponse
    {
        $chat = Chat::create();
        return response()->json([
            'chatId'    => $chat->id,
            'createdAt' => $chat->created_at,
        ], 201);
    }
}
