<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\TrackingSession;
use App\Models\ActivityEvent;
use Illuminate\Http\Request;

class FindingController extends Controller
{
    public function index(Request $request)
    {
        $query = TrackingSession::query()
            ->where('status', 'verified')
            ->whereHas('events', function ($q) {
                $q->where('status', 'submitted');
            })
            ->withCount(['events as pending_events_count' => function ($q) {
                $q->where('status', 'submitted');
            }]);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . (string) $request->string('q') . '%');
        }

        $sessions = $query
            ->latest('start_time')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total_pending_sessions' => TrackingSession::where('status', 'verified')
                ->whereHas('events', fn($q) => $q->where('status', 'submitted'))->count(),
            'total_pending_findings' => ActivityEvent::where('status', 'submitted')
                ->whereHas('session', fn($q) => $q->where('status', 'verified'))->count(),
        ];

        return view('verifications.findings.index', compact('sessions', 'summary'));
    }

    public function review(TrackingSession $session, Request $request)
    {
        if ($session->status !== 'verified') {
            abort(404);
        }

        $event = $session->events()
            ->where('status', 'submitted')
            ->orderBy('timestamp')
            ->first();

        if (!$event) {
            return redirect('verifications/findings')
                ->with('success', 'Semua temuan di perjalanan "' . ($session->title ?? 'Tanpa Nama') . '" telah tuntas divalidasi!');
        }

        $event->load('photos');
        
        $remainingCount = $session->events()->where('status', 'submitted')->count();
        $totalCount = $session->events()->count();
        $progress = $totalCount > 0 ? (($totalCount - $remainingCount) / $totalCount) * 100 : 0;

        return view('verifications.findings.review', compact('session', 'event', 'remainingCount', 'totalCount', 'progress'));
    }

    public function verify(Request $request, TrackingSession $session, ActivityEvent $event)
    {
        if ($session->id !== $event->session_id) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
        ]);

        $event->update([
            'status' => $validated['action'] === 'verify' ? 'verified' : 'rejected'
        ]);

        return redirect()->route('verifications.findings.review', $session);
    }
}
