<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackingSession;
use Illuminate\Http\JsonResponse;

/** TASK-238: /api/sync/status/{id} */
class SyncStatusDetailController extends Controller
{
    public function show(string $id): JsonResponse
    {
        $session = TrackingSession::find($id);

        if (!$session) {
            return response()->json([
                'status' => 'pending_or_processing',
                'session_id' => $id,
            ], 200);
        }

        return response()->json([
            'status' => $session->status,
            'session_id' => $id,
            'title' => $session->title,
            'updated_at' => $session->updated_at?->toIso8601String(),
        ], 200);
    }
}
