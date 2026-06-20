<?php

namespace App\Http\Controllers;

use App\Models\TrackingSession;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $month = $month >= 1 && $month <= 12 ? $month : now()->month;
        $year = $year >= 2000 && $year <= 2100 ? $year : now()->year;

        $sessions = TrackingSession::query()
            ->where('status', 'verified')
            ->with([
                'trackPoints' => fn ($query) => $query->orderBy('timestamp'),
                'events',
            ])
            ->withCount(['events', 'photos', 'trackPoints'])
            ->whereYear('start_time', $year)
            ->whereMonth('start_time', $month)
            ->orderByDesc('start_time')
            ->get();

        $routes = $sessions->map(function ($session, $index) {
            return [
                'id' => $session->id,
                'title' => $session->title ?? 'Perjalanan Tanpa Nama',
                'start_time' => optional($session->start_time)->format('d M Y, H:i'),
                'distance' => (float) $session->distance,
                'duration_seconds' => (int) $session->duration_seconds,
                'events_count' => $session->events_count,
                'photos_count' => $session->photos_count,
                'track_points_count' => $session->track_points_count,
                'color' => $this->routeColor($index),
                'url' => url('activities', $session),
                'coordinates' => $session->trackPoints
                    ->filter(fn ($point) => $point->latitude !== null && $point->longitude !== null)
                    ->map(fn ($point) => [(float) $point->latitude, (float) $point->longitude])
                    ->values(),
                'findings' => $session->events
                    ->filter(fn ($event) => $event->latitude !== null && $event->longitude !== null)
                    ->map(fn ($event) => [
                        'id' => $event->id,
                        'title' => $event->title ?? 'Temuan Tanpa Judul',
                        'status' => $event->status,
                        'latitude' => (float) $event->latitude,
                        'longitude' => (float) $event->longitude,
                        'url' => url('findings', $event),
                    ])
                    ->values(),
            ];
        });

        $years = TrackingSession::query()
            ->where('status', 'verified')
            ->whereNotNull('start_time')
            ->orderByDesc('start_time')
            ->get(['start_time'])
            ->map(fn ($session) => $session->start_time->year)
            ->unique()
            ->values();

        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        $summary = [
            'total_routes' => $sessions->count(),
            'total_distance' => (float) $sessions->sum('distance'),
            'total_findings' => (int) $sessions->flatMap->events->where('status', 'verified')->count(),
            'total_track_points' => (int) $sessions->sum('track_points_count'),
        ];

        return view('maps.index', compact('sessions', 'routes', 'month', 'year', 'years', 'summary'));
    }

    private function routeColor(int $index): string
    {
        $colors = ['#2563eb', '#e11d48', '#059669', '#d97706', '#7c3aed', '#0891b2', '#be123c', '#4f46e5'];

        return $colors[$index % count($colors)];
    }
}
