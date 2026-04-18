<?php

namespace App\Http\Controllers;

use App\Models\BroadcastMessage;
use Carbon\Carbon;
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
            'created_by' => auth()->id() ?? 1,
            'expires_at' => Carbon::now()->addMinutes(15),
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
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('created_at')
            ->first();

        return response()->json([
            'message' => $message?->message,
            'expires_at' => optional($message?->expires_at)?->toIso8601String(),
        ]);
    }

    /**
     * Clear all OMM/broadcast messages.
     */
    public function clear(): JsonResponse
    {
        BroadcastMessage::query()
            ->where('message', 'not like', 'EMERGENCY:%')
            ->delete();

        return response()->json([
            'message' => null,
        ]);
    }

    /**
     * Store emergency mode code (persists until manually cleared).
     */
    public function storeEmergency(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'in:Code Red,Code Blue,Code Yellow,Code Black,Code Orange,Lockdown,Evacuation'],
        ]);

        BroadcastMessage::query()
            ->where('message', 'like', 'EMERGENCY:%')
            ->delete();

        $message = BroadcastMessage::create([
            'message' => 'EMERGENCY:'.$validated['code'],
            'created_by' => auth()->id() ?? 1,
            'expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'id' => $message->id,
            'code' => $validated['code'],
        ], 201);
    }

    /**
     * Clear only emergency mode records.
     */
    public function clearEmergency(): JsonResponse
    {
        BroadcastMessage::query()
            ->where('message', 'like', 'EMERGENCY:%')
            ->delete();

        return response()->json(['success' => true]);
    }
}
