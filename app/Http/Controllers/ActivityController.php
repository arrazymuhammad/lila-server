<?php
namespace App\Http\Controllers;

use App\Models\TrackingSession;

class ActivityController extends Controller {
    public function show(TrackingSession $session) {
        $session->load(['trackPoints', 'events.photos']);

        return view('activities.show', compact('session'));
    }
}

