<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackingSession;

class SessionStatusController extends Controller
{
    public function check()
    {
        $ids = request()->input('session_ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['message' => 'session_ids required'], 422);
        }

        /** @disregard Intelephense false positive — whereIn accepts array arg */
        $sessions = TrackingSession::whereIn('id', $ids)
            ->select('id', 'status')
            ->get()
            ->pluck('status', 'id');

        return response()->json($sessions);
    }
}
