<?php
namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\ActivityPhoto;
use App\Models\TrackingSession;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = TrackingSession::query()
            ->where('status', 'verified')
            ->with('mobileUser')
            ->withCount([
                'events' => fn($q) => $q->where('status', 'verified'),
                'photos' => fn($q) => $q->where('selected', true)->whereHas('event', fn($e) => $e->where('status', 'verified')),
                'trackPoints',
            ]);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . (string) $request->string('q') . '%');
        }

        if ($request->boolean('has_findings')) {
            $query->whereHas('events', fn($q) => $q->where('status', 'verified'));
        }

        match ($request->input('sort')) {
            'distance' => $query->orderByDesc('distance'),
            'duration' => $query->orderByDesc('duration_seconds'),
            'events' => $query->orderByDesc('events_count'),
            'photos' => $query->orderByDesc('photos_count'),
            default => $query->latest('start_time'),
        };

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $month = $month >= 1 && $month <= 12 ? $month : now()->month;
        $year = $year >= 2000 && $year <= 2100 ? $year : now()->year;

        $query->whereYear('start_time', $year)->whereMonth('start_time', $month);

        $sessions = $query
            ->paginate(12)
            ->withQueryString();

        $baseQuery = TrackingSession::where('status', 'verified')
            ->whereYear('start_time', $year)
            ->whereMonth('start_time', $month);

        $summary = [
            'total_sessions' => (clone $baseQuery)->count(),
            'total_distance' => (float) (clone $baseQuery)->sum('distance'),
            'total_duration' => (int) (clone $baseQuery)->sum('duration_seconds'),
            'total_events' => ActivityEvent::where('status', 'verified')->whereHas('session', fn($q) => $q->where('status', 'verified')->whereYear('start_time', $year)->whereMonth('start_time', $month))->count(),
            'total_photos' => ActivityPhoto::where('selected', true)->whereHas('event', fn($q) => $q->where('status', 'verified'))->whereHas('session', fn($q) => $q->where('status', 'verified')->whereYear('start_time', $year)->whereMonth('start_time', $month))->count(),
        ];

        $years = TrackingSession::query()
            ->where('status', 'verified')
            ->whereNotNull('start_time')
            ->orderByDesc('start_time')
            ->get(['start_time'])
            ->map(fn ($session) => $session->start_time->year)
            ->unique()
            ->values();

        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        return view('activities.index', compact('sessions', 'summary', 'month', 'year', 'years'));
    }

    public function show(TrackingSession $session)
    {
        $session->load([
            'mobileUser',
            'trackPoints' => fn ($query) => $query->orderBy('timestamp'),
            'photos' => fn ($query) => $query->where('selected', true)->whereHas('event', fn($e) => $e->where('status', 'verified'))->latest('timestamp'),
            'events' => fn ($query) => $query->where('status', 'verified')->with(['photos' => fn ($q) => $q->where('selected', true)->latest('timestamp')]),
        ]);

        $summary = [
            'track_points' => $session->trackPoints->count(),
            'events' => $session->events->count(),
            'photos' => $session->photos->count(),
            'selected_photos' => $session->photos->where('selected', true)->count(),
            'first_point' => $session->trackPoints->first(),
            'last_point' => $session->trackPoints->last(),
        ];

        return view('activities.show', compact('session', 'summary'));
    }
}
