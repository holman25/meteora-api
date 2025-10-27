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
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $ids = explode(',', $request->query('ids', ''));

        if (empty($ids) || !is_array($ids)) {
            return response()->json([], 200);
        }

        $chats = Chat::whereIn('id', $ids)
            ->with(['messages' => fn($query) => $query->latest()->take(1)])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($chats);
    }
}
