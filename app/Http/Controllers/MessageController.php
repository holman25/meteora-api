<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Orchestrators\ChatOrchestrator;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MessageStoreRequest;

class MessageController extends Controller
{
    public function index(Chat $chat): JsonResponse
    {
        $messages = $chat->messages()->orderBy('created_at')->get();
        return response()->json($messages);
    }

    public function store(Chat $chat, MessageStoreRequest $request, ChatOrchestrator $orchestrator)
    {
        $result = $orchestrator->handle($chat, $request->validated());
        return response()->json($result, 201);
    }

    public function retry(Message $message, ChatOrchestrator $orchestrator): \Illuminate\Http\JsonResponse
    {
        abort_unless($message->role === 'user', 422, 'Solo se reintentan respuestas a mensajes de usuario.');

        $payload = [
            'content'  => $message->content,
            'date'     => data_get($message->metadata, 'date'),
            'location' => data_get($message->metadata, 'location'),
        ];

        $result = $orchestrator->handle($message->chat, $payload);

        return response()->json(['assistant' => $result['assistant']]);
    }
}
