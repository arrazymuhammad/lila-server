<?php

namespace App\Http\Controllers;

use App\Models\TrackingSession;
use App\Models\VerificationAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Verify or reject a session. Persists rejected_reason (TASK-132)
     * and logs audit trail (TASK-133).
     */
    public function verify(Request $request, TrackingSession $session)
    {
        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'reason' => 'required_if:action,reject|string|nullable',
        ]);

        $newStatus = $validated['action'] === 'verify' ? 'verified' : 'rejected';
        $oldStatus = $session->status;
        $reason = $validated['reason'] ?? null;

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'rejected') {
            $updateData['rejected_reason'] = $this->sanitize($reason);
        } else {
            $updateData['rejected_reason'] = null; // clear on re-verify
        }

        $session->update($updateData);

        // TASK-133: Audit trail
        VerificationAuditTrail::create([
            'session_id' => $session->id,
            'action' => $validated['action'],
            'verifier_name' => Auth::user()?->name ?? 'system',
            'reason' => $this->sanitize($reason),
            'changes' => ['status' => [$oldStatus, $newStatus]],
        ]);

        return redirect()->back()->with('success', "Perjalanan berhasil di{$validated['action']}.");
    }

    /** TASK-120: Basic XSS sanitization for text inputs */
    private function sanitize(?string $input): ?string
    {
        if ($input === null) return null;
        return strip_tags($input);
    }
}
