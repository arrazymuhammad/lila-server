<?php
namespace App\Http\Controllers;

use App\Models\TrackingSession;

class ActivityController extends Controller
{
    public function index()
    {
        $sessions = TrackingSession::query()
            ->withCount([
                'events',
                'photos'
            ])
            ->latest('start_time')
            ->paginate();
        return view('activities.index', compact('sessions'));
    }
    public function show(TrackingSession $session)
    {
        $session->load(['trackPoints', 'photos', 'events.photos']);
        return view('activities.show', compact('session'));
    }
}

