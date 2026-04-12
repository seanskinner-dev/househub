<?php

namespace App\Http\Controllers;

use App\Models\OfficeMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfficeMessageController extends Controller
{
    /**
     * Store a newly created office message.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'message' => ['required', 'string'],
        ]);

        $officeMessage = OfficeMessage::create([
            'student_id' => $validated['student_id'],
            'message' => $validated['message'],
            'status' => 'pending',
            'created_by' => $request->user()->id,
        ]);

        return response()->json($officeMessage->load(['student', 'creator']), 201);
    }

    /**
     * Mark the office message as acknowledged.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $officeMessage = OfficeMessage::findOrFail($id);

        $officeMessage->update([
            'status' => 'acknowledged',
        ]);

        return response()->json($officeMessage->fresh()->load(['student', 'creator']));
    }
}
