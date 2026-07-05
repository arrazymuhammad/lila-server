<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeatmapController extends Controller
{
    /**
     * Get heatmap data points for verified activity events.
     */
    public function index(Request $request): JsonResponse
    {
        $days = max(1, min((int) $request->input('days', 30), 365));
        $since = now()->subDays($days);

        $query = DB::table('activity_events')
            ->select('latitude', 'longitude')
            ->where('status', 'verified')
            ->where('timestamp', '>=', $since);

        if ($request->has('category') && $request->input('category') !== 'all') {
            $query->where('operator_category', $request->input('category'));
        }

        $points = $query->get()->map(function ($event) {
            return [
                'lat' => (float) $event->latitude,
                'lng' => (float) $event->longitude,
                'weight' => 1.0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'days' => $days,
            'count' => $points->count(),
            'data' => $points,
        ]);
    }
}
