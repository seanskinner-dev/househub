<?php

namespace App\Http\Controllers;

use App\Models\BroadcastMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BroadcastMessageController extends Controller
{
    /**
     * Store a new broadcast message.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $broadcastMessage = BroadcastMessage::create([
            'message' => $validated['message'],
            'created_by' => $request->user()->id,
        ]);

        return response()->json($broadcastMessage->load('creator'), 201);
    }

    /**
     * Return the most recent broadcast message as JSON (or null if none exist).
     */
    public function latest(): JsonResponse
    {
        $message = BroadcastMessage::query()
            ->with('creator')
            ->latest('created_at')
            ->first();

        return response()->json($message);
    }
}
