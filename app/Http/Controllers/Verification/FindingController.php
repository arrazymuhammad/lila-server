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

        $event->load(['photos', 'mobileUser', 'transcribedBy']);

        $remainingCount = $session->events()->where('status', 'submitted')->count();
        $totalCount = $session->events()->count();
        $progress = $totalCount > 0 ? (($totalCount - $remainingCount) / $totalCount) * 100 : 0;

        $suggestedCategories = \App\Models\FindingCategory::orderBy('name')->pluck('name');

        return view('verifications.findings.review', compact('session', 'event', 'remainingCount', 'totalCount', 'progress', 'suggestedCategories'));
    }

    public function verify(Request $request, TrackingSession $session, ActivityEvent $event)
    {
        if ($session->id !== $event->session_id) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'title' => 'nullable|string|max:255',
            'operator_category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'voice_note_transcription' => 'nullable|string',
            'selected_photos' => 'nullable|array',
            'selected_photos.*' => 'string|exists:activity_photos,id'
        ]);

        if ($validated['action'] === 'verify') {
            $totalPhotos = $event->photos()->count();
            $selectedCount = count($validated['selected_photos'] ?? []);
            if ($totalPhotos > 0 && $selectedCount <= 0) {
                return back()->withErrors(['action' => 'Temuan tidak dapat diverifikasi jika seluruh bukti fotonya ditolak. Sisakan minimal 1 foto, atau Tolak temuan ini secara keseluruhan.']);
            }
        }

        $updateData = [
            'status' => $validated['action'] === 'verify' ? 'verified' : 'rejected',
            'title' => $validated['title'] ?? $event->title,
            'description' => $validated['description'] ?? $event->description,
        ];

        if (array_key_exists('voice_note_transcription', $validated)) {
            $updateData['voice_note_transcription'] = $validated['voice_note_transcription'];
            $updateData['transcribed_by'] = !empty($validated['voice_note_transcription']) ? auth()->id() : null;
        }

        $event->update($updateData);

        if (array_key_exists('operator_category', $validated)) {
            $event->operator_category = $validated['operator_category'];
            $event->save();
        }

        // Checkbox is opt-out now (checked = kept as evidence by default): unchecked
        // boxes simply aren't submitted, so anything not in selected_photos is rejected.
        $selectedPhotoIds = $validated['selected_photos'] ?? [];
        $event->photos()->whereIn('id', $selectedPhotoIds)->update(['selected' => true]);
        $event->photos()->whereNotIn('id', $selectedPhotoIds)->update(['selected' => false]);

        return redirect()->route('verifications.findings.review', $session);
    }
}
