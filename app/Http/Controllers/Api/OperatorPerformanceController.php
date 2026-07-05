<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorPerformanceController extends Controller
{
    /**
     * Rank users (operators) by distance and verified findings.
     */
    public function index(Request $request): JsonResponse
    {
        $days = max(1, min((int) $request->input('days', 30), 365));
        $limit = max(1, min((int) $request->input('limit', 10), 50));
        $since = now()->subDays($days);

        $operators = DB::table('tracking_sessions')
            ->join('users', 'users.id', '=', 'tracking_sessions.user_id')
            ->select(
                'users.name as operator_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(CASE WHEN tracking_sessions.status = \'verified\' THEN 1 ELSE 0 END) as verified_sessions'),
                DB::raw('COALESCE(SUM(tracking_sessions.distance), 0) as total_distance'),
                DB::raw('COALESCE(SUM(tracking_sessions.duration_seconds), 0) as total_duration')
            )
            ->where('tracking_sessions.start_time', '>=', $since)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_distance')
            ->limit($limit)
            ->get();

        // Enrich with finding counts
        $userNames = $operators->pluck('operator_name')->toArray();
        $findingCounts = DB::table('activity_events')
            ->join('tracking_sessions', 'tracking_sessions.id', '=', 'activity_events.session_id')
            ->join('users', 'users.id', '=', 'tracking_sessions.user_id')
            ->whereIn('users.name', $userNames)
            ->where('activity_events.status', 'verified')
            ->where('tracking_sessions.start_time', '>=', $since)
            ->groupBy('users.name')
            ->pluck(DB::raw('COUNT(*) as finding_count'), 'users.name');

        $data = $operators->map(function ($op) use ($findingCounts) {
            return [
                'operator_name' => $op->operator_name,
                'total_sessions' => (int) $op->total_sessions,
                'verified_sessions' => (int) $op->verified_sessions,
                'total_distance' => (float) $op->total_distance,
                'total_duration' => (int) $op->total_duration,
                'verified_findings' => (int) ($findingCounts[$op->operator_name] ?? 0),
            ];
        });

        return response()->json([
            'status' => 'success',
            'days' => $days,
            'data' => $data,
        ]);
    }
}
