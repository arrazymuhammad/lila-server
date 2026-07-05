<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityEvent;
use App\Models\ActivityPhoto;
use App\Models\TrackingSession;
use App\Models\TrackPoint;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SyncController extends Controller
{
    public function activity()
    {
        request()->validate([
            'file' => ['required', 'file', 'mimes:zip'],
        ]);

        $zipFile = request()->file('file');

        $extractPath = storage_path('app/temp/' . Str::uuid7());

        mkdir($extractPath, 0777, true);

        try {
            $zip = new ZipArchive();

            if (
                $zip->open($zipFile->getRealPath())
                !== true
            ) {
                return response()->json([
                    'message' => 'ZIP tidak valid',
                ], 400);
            }
            $zip->extractTo($extractPath);
            $zip->close();

            $metadataFile = $extractPath . '/metadata.json';

            if (!file_exists($metadataFile)) {
                return response()->json([
                    'message' => 'metadata.json tidak ditemukan',
                ], 400);
            }

            $metadata = json_decode(
                file_get_contents($metadataFile),
                true
            );

            $sessionId = $metadata['session']['id'] ?? null;

            if ($sessionId) {
                $existing = TrackingSession::find($sessionId);

                if ($existing && $existing->status === 'verified') {
                    return response()->json([
                        'message' => 'Sesi sudah terverifikasi. Tidak dapat melakukan sinkronisasi ulang.',
                    ], 409);
                }
            }

            $this->importSession(
                $metadata['session']
            );

            $this->importTrackPoints(
                $metadata['track_points'],
                $metadata['session']['id']
            );

            $this->importEvents(
                $metadata['events'],
                $metadata['session']['id']
            );

            $this->importPhotos(
                $metadata['photos'],
                $extractPath,
                $metadata['session']['id'],
            );

            return response()->json([
                'message' => 'Sinkronisasi berhasil',
            ]);
        } finally {
            if (is_dir($extractPath)) {
                Storage::disk('local')->deleteDirectory(
                    str_replace(storage_path('app/'), '', $extractPath)
                );
            }
        }
    }

    private function importSession(array $session): void
    {
        TrackingSession::updateOrCreate(
            ['id' => $session['id']],
            [
                ...$session,
                'status' => 'submitted'
            ]
        );
    }

    private function importTrackPoints(array $trackPoints, string $sessionId): void
    {
        TrackPoint::where('session_id', $sessionId)->delete();
        foreach ($trackPoints as $trackPoint) {
            $trackPoint['session_id'] = $sessionId;
            TrackPoint::create($trackPoint);
        }
    }

    private function importEvents(array $events, string $sessionId): void
    {
        foreach ($events as $event) {
            $event['session_id'] = $sessionId;

            $existing = ActivityEvent::find($event['id']);
            if (!$existing) {
                $event['status'] = 'submitted';
            }

            ActivityEvent::updateOrCreate(
                ['id' => $event['id']],
                $event
            );
        }
    }

    private function importPhotos(
        array $photos,
        string $extractPath,
        string $sessionId,
    ): void {
        $targetDir = public_path("activity-photos/{$sessionId}");

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        foreach ($photos as $photo) {

            $source = $extractPath . '/photos/' . $photo['filename'];
            $target = $targetDir . '/' . $photo['filename'];

            if (file_exists($source)) {
                copy($source, $target);
            }

            ActivityPhoto::updateOrCreate(
                ['id' => $photo['id']],
                [
                    'session_id' => $photo['sessionId'],
                    'event_id' => $photo['eventId'],
                    'file_path' => "activity-photos/{$sessionId}/{$photo['filename']}",
                    'filename' => $photo['filename'],
                    'latitude' => $photo['latitude'],
                    'longitude' => $photo['longitude'],
                    'timestamp' => $photo['timestamp'],
                    'selected' => $photo['selected'] ?? true,
                ]
            );
        }
    }
}
