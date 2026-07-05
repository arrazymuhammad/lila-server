<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** TASK-203: /api/analytics/category-trends - temuan per kategori 7 hari */
class CategoryTrendController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $days = max(1, min((int) $request->input('days', 7), 90));
        $since = now()->subDays($days);

        $trends = DB::table('activity_events')
            ->selectRaw('operator_category as category, DATE(timestamp) as date, COUNT(*) as count')
            ->where('status', 'verified')
            ->where('timestamp', '>=', $since)
            ->whereNotNull('operator_category')
            ->groupBy('operator_category', 'date')
            ->orderBy('date')
            ->get();

        return response()->json(['status' => 'success', 'data' => $trends]);
    }
}
