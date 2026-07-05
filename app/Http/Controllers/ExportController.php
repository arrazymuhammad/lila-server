<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\TrackingSession;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * TASK-136: Export verified tracking sessions to CSV.
     */
    public function sessions(Request $request): StreamedResponse
    {
        $query = TrackingSession::query()
            ->where('status', 'verified')
            ->withCount(['events', 'photos', 'trackPoints'])
            ->latest('start_time');

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('start_time', $request->year)->whereMonth('start_time', $request->month);
        }

        $filename = 'lila_sessions_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Judul', 'Waktu Mulai', 'Waktu Selesai', 'Jarak (km)', 'Durasi (detik)', 'Temuan', 'Foto', 'Titik Track']);

            $query->chunk(100, function ($sessions) use ($handle) {
                foreach ($sessions as $s) {
                    fputcsv($handle, [
                        $s->id,
                        $s->title,
                        $s->start_time,
                        $s->end_time,
                        $s->distance,
                        $s->duration_seconds,
                        $s->events_count,
                        $s->photos_count,
                        $s->track_points_count,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * TASK-136: Export verified findings to CSV.
     */
    public function findings(Request $request): StreamedResponse
    {
        $query = ActivityEvent::query()
            ->where('status', 'verified')
            ->whereHas('session', fn($q) => $q->where('status', 'verified'))
            ->with(['session:id,title'])
            ->latest('timestamp');

        if ($request->filled('category')) {
            $query->where('operator_category', $request->category);
        }

        $filename = 'lila_findings_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Sesi ID', 'Judul Sesi', 'Judul Temuan', 'Kategori', 'Waktu', 'Latitude', 'Longitude', 'Deskripsi']);

            $query->chunk(100, function ($findings) use ($handle) {
                foreach ($findings as $f) {
                    fputcsv($handle, [
                        $f->id,
                        $f->session_id,
                        $f->session?->title,
                        $f->title,
                        $f->operator_category,
                        $f->timestamp,
                        $f->latitude,
                        $f->longitude,
                        $f->description,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
