<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityEvent;
use App\Models\ActivityPhoto;
use App\Models\TrackingSession;
use App\Models\TrackPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SyncController extends Controller
{
    /**
     * TASK-106: Standardized JSON error responses with code + timestamp.
     * TASK-112: Detailed logging for every sync event.
     */
    private function error(string $message, int $status, string $code = 'SYNC_ERROR'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ], $status);
    }

    public function activity()
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        request()->validate([
            'file' => ['required', 'file', 'mimes:zip', 'max:100000'],
        ]);

        $zipFile = request()->file('file');

        $extractPath = storage_path('app/temp/' . Str::uuid7());

        mkdir($extractPath, 0777, true);

        try {
            $zip = new ZipArchive();

            if ($zip->open($zipFile->getRealPath()) !== true) {
                Log::warning('Sync: invalid ZIP', ['ip' => $ip]);
                return $this->error('ZIP tidak valid', 400, 'INVALID_ZIP');
            }

            // Zip Slip protection: validate all entry paths before extraction
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                $resolved = realpath($extractPath . '/' . $entryName);
                if ($resolved === false || !str_starts_with($resolved, realpath($extractPath))) {
                    $zip->close();
                    Log::warning('Sync: zip slip detected', ['entry' => $entryName, 'ip' => $ip]);
                    return $this->error('ZIP berisi path tidak valid', 400, 'ZIP_SLIP_DETECTED');
                }
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $metadataFile = $extractPath . '/metadata.json';

            if (!file_exists($metadataFile)) {
                Log::warning('Sync: metadata.json missing', ['ip' => $ip]);
                return $this->error('metadata.json tidak ditemukan', 400, 'METADATA_MISSING');
            }

            $metadata = json_decode(
                file_get_contents($metadataFile),
                true
            );

            $sessionId = $metadata['session']['id'] ?? null;

            if ($sessionId) {
                $existing = TrackingSession::find($sessionId);

                if ($existing && $existing->status === 'verified') {
                    Log::info('Sync: rejected verified session re-upload', ['session_id' => $sessionId, 'ip' => $ip]);
                    return $this->error('Sesi sudah terverifikasi. Tidak dapat melakukan sinkronisasi ulang.', 409, 'SESSION_ALREADY_VERIFIED');
                }
            }

            DB::transaction(function () use ($metadata, $extractPath, $sessionId) {
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
            });

            Log::info('Sync: success', ['session_id' => $sessionId, 'ip' => $ip, 'ua' => $userAgent]);

            return response()->json([
                'success' => true,
                'code' => 'SYNC_SUCCESS',
                'message' => 'Sinkronisasi berhasil',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Sync: exception', [
                'session_id' => $sessionId ?? null,
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);
            return $this->error('Terjadi kesalahan server saat sinkronisasi', 500, 'SYNC_EXCEPTION');
        } finally {
            if (is_dir($extractPath)) {
                Storage::disk('local')->deleteDirectory(
                    str_replace(storage_path('app/'), '', $extractPath)
                );
            }
        }
    }

    /** TASK-120: strip_tags on text fields to prevent XSS stored */
    private function sanitize(?string $value): ?string
    {
        return $value !== null ? strip_tags($value) : null;
    }

    private function importSession(array $session): void
    {
        TrackingSession::updateOrCreate(
            ['id' => $session['id']],
            [
                ...$session,
                'title' => $this->sanitize($session['title'] ?? null),
                'status' => 'submitted'
            ]
        );
    }

    /**
     * TASK-115: Deduplicate track points by (session_id, timestamp) before import.
     */
    private function importTrackPoints(array $trackPoints, string $sessionId): void
    {
        TrackPoint::where('session_id', $sessionId)->delete();

        $seen = [];
        $deduped = [];

        foreach ($trackPoints as $tp) {
            $key = $sessionId . '|' . ($tp['timestamp'] ?? '');
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $tp['session_id'] = $sessionId;
            $deduped[] = $tp;
        }

        foreach ($deduped as $trackPoint) {
            TrackPoint::create($trackPoint);
        }
    }

    private function importEvents(array $events, string $sessionId): void
    {
        foreach ($events as $event) {
            $event['session_id'] = $sessionId;
            // TASK-120: Sanitize text fields against XSS
            $event['title'] = $this->sanitize($event['title'] ?? null);
            $event['description'] = $this->sanitize($event['description'] ?? null);
            $event['operator_category'] = $this->sanitize($event['operator_category'] ?? null);

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

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        foreach ($photos as $photo) {

            $source = $extractPath . '/photos/' . $photo['filename'];
            $target = $targetDir . '/' . $photo['filename'];

            if (file_exists($source)) {
                $mime = mime_content_type($source);
                if (!in_array($mime, $allowedMimes, true)) {
                    continue;
                }
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
