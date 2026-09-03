<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\ActivityPhoto;
use App\Models\TrackPoint;
use App\Models\TrackingSession;

class DashboardController extends Controller
{
    private const RANGES = ['7d', '30d', '12m'];

    private const RANGE_LABELS = [
        '7d' => '7 Hari',
        '30d' => '30 Hari',
        '12m' => '12 Bulan',
    ];

    private const MONTH_LABELS_ID = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    public function index()
    {
        $range = request('range', '7d');
        if (!in_array($range, self::RANGES, true)) {
            $range = '7d';
        }
        $rangeLabel = self::RANGE_LABELS[$range];
        $rangeOptions = self::RANGE_LABELS;
        $totalSessions = TrackingSession::where('status', 'verified')->count();
        $totalDistance = (float) TrackingSession::where('status', 'verified')->sum('distance');
        $totalDuration = (int) TrackingSession::where('status', 'verified')->sum('duration_seconds');
        $totalEvents = ActivityEvent::where('status', 'verified')->whereHas('session', fn($q) => $q->where('status', 'verified'))->count();
        $totalPhotos = ActivityPhoto::where('selected', true)->whereHas('event', fn($q) => $q->where('status', 'verified'))->whereHas('session', fn($q) => $q->where('status', 'verified'))->count();

        $stats = [
            'total_sessions' => $totalSessions,
            'total_events' => $totalEvents,
            'total_photos' => $totalPhotos,
            'selected_photos' => ActivityPhoto::where('selected', true)->whereHas('event', fn($q) => $q->where('status', 'verified'))->whereHas('session', fn($q) => $q->where('status', 'verified'))->count(),
            'total_track_points' => TrackPoint::whereHas('session', fn($q) => $q->where('status', 'verified'))->count(),
            'total_distance' => $totalDistance,
            'total_duration' => $totalDuration,
            'avg_distance' => $totalSessions > 0 ? $totalDistance / $totalSessions : 0,
            'avg_duration' => $totalSessions > 0 ? (int) floor($totalDuration / $totalSessions) : 0,
            'events_per_session' => $totalSessions > 0 ? $totalEvents / $totalSessions : 0,
            'photos_per_session' => $totalSessions > 0 ? $totalPhotos / $totalSessions : 0,
        ];

        $latestActivities = TrackingSession::where('status', 'verified')
            ->latest('start_time')
            ->withCount(['events' => fn($q) => $q->where('status', 'verified'), 'photos' => fn($q) => $q->where('selected', true)->whereHas('event', fn($e) => $e->where('status', 'verified'))])
            ->take(8)
            ->get();

        $statusSummary = TrackingSession::query()
            ->where('status', 'verified')
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        if ($range === '12m') {
            $periods = collect(range(11, 0))->map(function ($monthsAgo) {
                $date = now()->subMonths($monthsAgo)->startOfMonth();

                return [
                    'key' => $date->format('Y-m'),
                    'label' => self::MONTH_LABELS_ID[(int) $date->format('n')] . ' ' . $date->format('y'),
                    'sessions' => 0,
                    'distance' => 0,
                    'events_count' => 0,
                ];
            });
            $since = now()->subMonths(11)->startOfMonth();
        } else {
            $daysBack = $range === '30d' ? 29 : 6;
            $periods = collect(range($daysBack, 0))->map(function ($daysAgo) {
                $date = now()->subDays($daysAgo);

                return [
                    'key' => $date->format('Y-m-d'),
                    'label' => $date->format('d M'),
                    'sessions' => 0,
                    'distance' => 0,
                    'events_count' => 0,
                ];
            });
            $since = now()->subDays($daysBack)->startOfDay();
        }

        $recentSessions = TrackingSession::query()
            ->where('status', 'verified')
            ->whereNotNull('start_time')
            ->where('start_time', '>=', $since)
            ->withCount(['events' => fn($q) => $q->where('status', 'verified')])
            ->get(['id', 'start_time', 'distance']);

        $activityTrend = $periods->map(function ($period) use ($recentSessions, $range) {
            $sessions = $recentSessions->filter(function ($session) use ($period, $range) {
                $key = $range === '12m'
                    ? $session->start_time?->format('Y-m')
                    : $session->start_time?->format('Y-m-d');

                return $key === $period['key'];
            });

            $period['sessions'] = $sessions->count();
            $period['distance'] = (float) $sessions->sum('distance');
            $period['events_count'] = (int) $sessions->sum('events_count');

            return $period;
        });

        $maxTrendDistance = max(1, (float) $activityTrend->max('distance'));
        $maxTrendEvents = max(1, (int) $activityTrend->max('events_count'));
        $maxTrendSessions = max(1, (int) $activityTrend->max('sessions'));

        $latestEvents = ActivityEvent::query()
            ->where('status', 'verified')
            ->whereHas('session', fn($q) => $q->where('status', 'verified'))
            ->with('session:id,title')
            ->latest('timestamp')
            ->take(5)
            ->get();

        $latestPhotos = ActivityPhoto::query()
            ->where('selected', true)
            ->whereHas('event', fn($q) => $q->where('status', 'verified'))
            ->whereHas('session', fn($q) => $q->where('status', 'verified'))
            ->with('session:id,title')
            ->latest('timestamp')
            ->take(6)
            ->get();

        $highlightSession = TrackingSession::query()
            ->where('status', 'verified')
            ->whereHas('events', fn($q) => $q->where('status', 'verified'))
            ->withCount(['events' => fn($q) => $q->where('status', 'verified'), 'photos' => fn($q) => $q->where('selected', true)->whereHas('event', fn($e) => $e->where('status', 'verified'))])
            ->orderByDesc('events_count')
            ->first();

        return view('dashboard', compact(
            'stats',
            'latestActivities',
            'statusSummary',
            'activityTrend',
            'maxTrendDistance',
            'maxTrendEvents',
            'maxTrendSessions',
            'latestEvents',
            'latestPhotos',
            'highlightSession',
            'range',
            'rangeLabel',
            'rangeOptions',
        ));
    }
}
