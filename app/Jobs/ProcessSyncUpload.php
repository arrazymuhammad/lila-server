<?php

namespace App\Jobs;

use App\Models\TrackingSession;
use App\Models\TrackPoint;
use App\Models\ActivityEvent;
use App\Models\ActivityPhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * TASK-155: Process a submitted sync zip in the background.
 * Extracts, validates, imports data, dispatches thumbnail jobs.
 */
class ProcessSyncUpload implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $sessionId,
        public string $zipPath,
    ) {}

    public function handle(): void
    {
        try {
            $zip = new ZipArchive();
            if ($zip->open($this->zipPath) !== true) {
                Log::error('SyncUpload: cannot open zip', ['session_id' => $this->sessionId]);
                return;
            }

            $extractPath = Storage::disk('local')->path("sync_extract/{$this->sessionId}");
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            // Zip Slip guard: ensure each entry resolves within extractPath
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if ($name === false) continue;
                $real = realpath($extractPath . '/' . $name);
                if ($real === false || !str_starts_with($real, realpath($extractPath))) continue;
                $zip->extractTo($extractPath, $name);
            }
            $zip->close();

            $jsonPath = $extractPath . '/data.json';
            if (!file_exists($jsonPath)) {
                Log::error('SyncUpload: missing data.json', ['session_id' => $this->sessionId]);
                return;
            }

            $data = json_decode(file_get_contents($jsonPath), true);
            if (!is_array($data)) {
                Log::error('SyncUpload: invalid data.json', ['session_id' => $this->sessionId]);
                return;
            }

            // upsert session
            TrackingSession::updateOrCreate(
                ['id' => $this->sessionId],
                [
                    'title' => $data['session']['title'] ?? null,
                    'start_time' => $data['session']['start_time'] ?? now(),
                    'end_time' => $data['session']['end_time'] ?? null,
                    'distance' => $data['session']['distance'] ?? 0,
                    'duration_seconds' => $data['session']['duration_seconds'] ?? 0,
                    'status' => 'submitted',
                ]
            );

            // upsert track points (dedup by timestamp)
            $seen = [];
            foreach ($data['track_points'] ?? [] as $tp) {
                $key = $this->sessionId . '|' . ($tp['timestamp'] ?? '');
                if (isset($seen[$key])) continue;
                $seen[$key] = true;
                TrackPoint::updateOrCreate(
                    ['session_id' => $this->sessionId, 'timestamp' => $tp['timestamp'] ?? now()],
                    ['latitude' => $tp['latitude'], 'longitude' => $tp['longitude'], 'accuracy' => $tp['accuracy'] ?? 0]
                );
            }

            // upsert events + photos, dispatch thumbnail jobs
            foreach ($data['events'] ?? [] as $ev) {
                ActivityEvent::updateOrCreate(
                    ['id' => $ev['id']],
                    [
                        'session_id' => $this->sessionId,
                        'title' => strip_tags($ev['title'] ?? ''),
                        'description' => strip_tags($ev['description'] ?? ''),
                        'category' => strip_tags($ev['operator_category'] ?? ''),
                        'latitude' => $ev['latitude'] ?? null,
                        'longitude' => $ev['longitude'] ?? null,
                        'timestamp' => $ev['timestamp'] ?? now(),
                        'status' => 'submitted',
                    ]
                );

                foreach ($ev['photos'] ?? [] as $ph) {
                    $photo = ActivityPhoto::updateOrCreate(
                        ['id' => $ph['id']],
                        [
                            'session_id' => $this->sessionId,
                            'event_id' => $ev['id'],
                            'file_path' => $ph['file_path'] ?? '',
                            'filename' => $ph['filename'] ?? '',
                            'latitude' => $ph['latitude'] ?? null,
                            'longitude' => $ph['longitude'] ?? null,
                            'timestamp' => $ph['timestamp'] ?? now(),
                            'selected' => $ph['selected'] ?? true,
                        ]
                    );
                    GeneratePhotoThumbnail::dispatch($photo->id);
                }
            }

            Log::info('SyncUpload completed', ['session_id' => $this->sessionId]);
        } catch (\Throwable $e) {
            Log::error('SyncUpload failed', ['session_id' => $this->sessionId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
