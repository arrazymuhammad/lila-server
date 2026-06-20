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
            ->withCount([
                'events',
                'photos',
                'trackPoints',
            ]);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . (string) $request->string('q') . '%');
        }

        if ($request->filled('status')) {
            $request->input('status') === '__unknown'
                ? $query->whereNull('status')
                : $query->where('status', (string) $request->string('status'));
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

        $summary = [
            'total_sessions' => TrackingSession::count(),
            'total_distance' => (float) TrackingSession::sum('distance'),
            'total_duration' => (int) TrackingSession::sum('duration_seconds'),
            'total_events' => ActivityEvent::count(),
            'total_photos' => ActivityPhoto::count(),
        ];

        $statuses = TrackingSession::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return view('activities.index', compact('sessions', 'summary', 'statuses'));
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

    public function verify(Request $request, TrackingSession $session)
    {
        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'reason' => 'required_if:action,reject|string|nullable',
        ]);

        $session->update([
            'status' => $validated['action'] === 'verify' ? 'verified' : 'rejected'
        ]);

        return redirect()->back();
    }
}
