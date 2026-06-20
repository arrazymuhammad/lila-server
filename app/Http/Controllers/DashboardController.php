<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\ActivityPhoto;
use App\Models\TrackPoint;
use App\Models\TrackingSession;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSessions = TrackingSession::count();
        $totalDistance = (float) TrackingSession::sum('distance');
        $totalDuration = (int) TrackingSession::sum('duration_seconds');
        $totalEvents = ActivityEvent::count();
        $totalPhotos = ActivityPhoto::count();

        $stats = [
            'total_sessions' => $totalSessions,
            'total_events' => $totalEvents,
            'total_photos' => $totalPhotos,
            'selected_photos' => ActivityPhoto::where('selected', true)->count(),
            'total_track_points' => TrackPoint::count(),
            'total_distance' => $totalDistance,
            'total_duration' => $totalDuration,
            'avg_distance' => $totalSessions > 0 ? $totalDistance / $totalSessions : 0,
            'avg_duration' => $totalSessions > 0 ? (int) floor($totalDuration / $totalSessions) : 0,
            'events_per_session' => $totalSessions > 0 ? $totalEvents / $totalSessions : 0,
            'photos_per_session' => $totalSessions > 0 ? $totalPhotos / $totalSessions : 0,
        ];

        $latestActivities = TrackingSession::latest('start_time')
            ->withCount(['events', 'photos'])
            ->take(8)
            ->get();

        $statusSummary = TrackingSession::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $lastSevenDays = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'key' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'sessions' => 0,
                'distance' => 0,
            ];
        });

        $recentSessions = TrackingSession::query()
            ->whereNotNull('start_time')
            ->where('start_time', '>=', now()->subDays(6)->startOfDay())
            ->get(['start_time', 'distance']);

        $activityTrend = $lastSevenDays->map(function ($day) use ($recentSessions) {
            $sessions = $recentSessions->filter(function ($session) use ($day) {
                return $session->start_time?->format('Y-m-d') === $day['key'];
            });

            $day['sessions'] = $sessions->count();
            $day['distance'] = (float) $sessions->sum('distance');

            return $day;
        });

        $maxTrendDistance = max(1, (float) $activityTrend->max('distance'));

        $latestEvents = ActivityEvent::query()
            ->with('session:id,title')
            ->latest('timestamp')
            ->take(5)
            ->get();

        $latestPhotos = ActivityPhoto::query()
            ->with('session:id,title')
            ->latest('timestamp')
            ->take(6)
            ->get();

        $highlightSession = TrackingSession::query()
            ->withCount(['events', 'photos'])
            ->orderByDesc('distance')
            ->first();

        return view('dashboard', compact(
            'stats',
            'latestActivities',
            'statusSummary',
            'activityTrend',
            'maxTrendDistance',
            'latestEvents',
            'latestPhotos',
            'highlightSession',
        ));
    }
}
