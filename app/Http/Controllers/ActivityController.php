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
            ->withCount([
                'events',
                'photos',
                'trackPoints',
            ]);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . (string) $request->string('q') . '%');
        }

        match ($request->input('sort')) {
            'distance' => $query->orderByDesc('distance'),
            'duration' => $query->orderByDesc('duration_seconds'),
            'events' => $query->orderByDesc('events_count'),
            'photos' => $query->orderByDesc('photos_count'),
            default => $query->latest('start_time'),
        };

        $sessions = $query
            ->paginate(12)
            ->withQueryString();

        $baseQuery = TrackingSession::where('status', 'verified');
        $summary = [
            'total_sessions' => (clone $baseQuery)->count(),
            'total_distance' => (float) (clone $baseQuery)->sum('distance'),
            'total_duration' => (int) (clone $baseQuery)->sum('duration_seconds'),
            'total_events' => ActivityEvent::whereHas('session', fn($q) => $q->where('status', 'verified'))->count(),
            'total_photos' => ActivityPhoto::whereHas('session', fn($q) => $q->where('status', 'verified'))->count(),
        ];

        return view('activities.index', compact('sessions', 'summary'));
    }

    public function show(TrackingSession $session)
    {
        $session->load([
            'trackPoints' => fn ($query) => $query->orderBy('timestamp'),
            'photos' => fn ($query) => $query->latest('timestamp'),
            'events.photos' => fn ($query) => $query->latest('timestamp'),
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
