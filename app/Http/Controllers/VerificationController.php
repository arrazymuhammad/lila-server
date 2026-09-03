<?php

namespace App\Http\Controllers;

use App\Models\TrackingSession;
use App\Models\VerificationAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = TrackingSession::query()
            ->whereIn('status', ['submitted', 'rejected'])
            ->with('mobileUser')
            ->withCount(['events', 'photos', 'trackPoints']);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . (string) $request->string('q') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->string('status'));
        }

        // TASK-128: Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->string('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->string('date_to'));
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
    /**
     * TASK-140: Bulk verify or reject multiple sessions.
     */
    public function bulkVerify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_ids' => 'required|array|min:1',
            'session_ids.*' => 'string',
            'action' => 'required|in:verify,reject',
            'reason' => 'required_if:action,reject|string|nullable',
        ]);

        $action = $validated['action'];
        $newStatus = $action === 'verify' ? 'verified' : 'rejected';
        $reason = $validated['reason'] ?? null;

        $sessions = TrackingSession::whereIn('id', $validated['session_ids'])
            ->whereIn('status', ['submitted', 'rejected'])
            ->get();

        foreach ($sessions as $session) {
            $oldStatus = $session->status;
            $updateData = ['status' => $newStatus];
            if ($newStatus === 'rejected') {
                $updateData['rejected_reason'] = $this->sanitize($reason);
            } else {
                $updateData['rejected_reason'] = null;
            }
            $session->update($updateData);

            VerificationAuditTrail::create([
                'session_id' => $session->id,
                'action' => 'bulk_' . $action,
                'verifier_name' => Auth::user()?->name ?? 'system',
                'reason' => $this->sanitize($reason),
                'changes' => ['status' => [$oldStatus, $newStatus]],
            ]);
        }

        $count = $sessions->count();
        return redirect()->back()->with('success', "{$count} perjalanan berhasil di{$action}.");
    }

    public function verify(Request $request, TrackingSession $session): RedirectResponse
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
