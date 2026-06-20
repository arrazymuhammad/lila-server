<?php

namespace App\Http\Controllers;

use App\Models\TrackingSession;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = TrackingSession::query()
            ->whereIn('status', ['submitted', 'rejected'])
            ->withCount(['events', 'photos', 'trackPoints']);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . (string) $request->string('q') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->string('status'));
        }

        $sessions = $query
            ->latest('start_time')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total_submitted' => TrackingSession::where('status', 'submitted')->count(),
            'total_rejected' => TrackingSession::where('status', 'rejected')->count(),
        ];

        return view('verifications.index', compact('sessions', 'summary'));
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
