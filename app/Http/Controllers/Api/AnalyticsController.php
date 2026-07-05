<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** TASK-209: /api/analytics/trends */
class AnalyticsController extends Controller
{
    public function trends(Request $request): JsonResponse
    {
        $days = (int) $request->input('days', 7);
        $days = max(1, min($days, 90));
        $since = now()->subDays($days)->format('Y-m-d');

        $trends = DB::table('daily_sync_summary')
            ->where('date', '>=', $since)
            ->orderBy('date')
            ->get();

        return response()->json([
            'status' => 'success',
            'days' => $days,
            'data' => $trends,
        ]);
    }
}
