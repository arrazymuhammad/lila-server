<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\TrackingSession;
use Illuminate\Http\Request;

class FindingController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityEvent::query()
            ->whereHas('session', fn($q) => $q->where('status', 'verified'))
            ->with(['session:id,title,start_time', 'photos'])
            ->withCount('photos');

        if ($request->filled('q')) {
            $keyword = '%' . (string) $request->string('q') . '%';

            $query->where(function ($finding) use ($keyword) {
                $finding
                    ->where('title', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('timestamp', $request->date('date')->format('Y-m-d'));
        }

        if ($request->filled('session_id')) {
            $query->where('session_id', (string) $request->string('session_id'));
        }

        $findings = $query
            ->latest('timestamp')
            ->paginate(12)
            ->withQueryString();

        $sessions = TrackingSession::query()
            ->where('status', 'verified')
            ->whereHas('events')
            ->orderByDesc('start_time')
            ->get(['id', 'title', 'start_time']);

        $baseEventQuery = ActivityEvent::whereHas('session', fn($q) => $q->where('status', 'verified'));

        $summary = [
            'total_findings' => (clone $baseEventQuery)->count(),
            'with_photos' => (clone $baseEventQuery)->has('photos')->count(),
            'with_coordinates' => (clone $baseEventQuery)->whereNotNull('latitude')->whereNotNull('longitude')->count(),
            'journeys_with_findings' => TrackingSession::where('status', 'verified')->whereHas('events')->count(),
        ];

        return view('findings.index', compact('findings', 'sessions', 'summary'));
    }

    public function show(ActivityEvent $event)
    {
        $event->load([
            'session.trackPoints' => fn ($query) => $query->orderBy('timestamp'),
            'photos' => fn ($query) => $query->latest('timestamp'),
        ]);

        return view('findings.show', compact('event'));
    }
}
