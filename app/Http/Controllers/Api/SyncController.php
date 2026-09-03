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

            // Zip Slip protection: check entry names for directory traversal before extraction
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if (str_contains($entryName, '..') || str_starts_with($entryName, '/')) {
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

            // TASK-116: Validate metadata structure (minimal schema check)
            if (!is_array($metadata) || empty($metadata['session']['id'])) {
                return $this->error('metadata.json format tidak valid', 400, 'METADATA_INVALID');
            }

            $sessionId = $metadata['session']['id'] ?? null;

            // session.id flows unvalidated into public_path() below (photos/audio storage
            // paths) — it must be a well-formed UUID, never a raw client-controlled string,
            // to prevent path traversal outside the intended per-session directory.
            if (!$sessionId || !Str::isUuid($sessionId)) {
                Log::warning('Sync: invalid session id format', ['session_id' => $sessionId, 'ip' => $ip]);
                return $this->error('metadata.json format tidak valid', 400, 'METADATA_INVALID');
            }

            if ($sessionId) {
                $existing = TrackingSession::find($sessionId);

                if ($existing && $existing->status === 'verified') {
                    Log::info('Sync: rejected verified session re-upload', ['session_id' => $sessionId, 'ip' => $ip]);
                    return $this->error('Sesi sudah terverifikasi. Tidak dapat melakukan sinkronisasi ulang.', 409, 'SESSION_ALREADY_VERIFIED');
                }
            }

            // Attribution is resolved server-side from the authenticated request
            // (set by MobileUserTokenMiddleware), never trusted from client JSON.
            $authenticatedMobileUserId = request()->attributes->get('mobile_user_id');

            DB::transaction(function () use ($metadata, $extractPath, $sessionId, $authenticatedMobileUserId) {
                $this->importSession(
                    $metadata['session'],
                    $authenticatedMobileUserId
                );

                $this->importTrackPoints(
                    $metadata['track_points'],
                    $metadata['session']['id']
                );

                $this->importEvents(
                    $metadata['events'],
                    $extractPath,
                    $metadata['session']['id'],
                    $authenticatedMobileUserId
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

    private function importSession(array $session, ?string $mobileUserId): void
    {
        // Explicit allowlist — never spread the raw client array (it could contain
        // a client-controlled `user_id`, which is fillable on TrackingSession and
        // would otherwise let a tampered client attribute a session to any admin user).
        TrackingSession::updateOrCreate(
            ['id' => $session['id']],
            [
                'title' => $this->sanitize($session['title'] ?? null),
                'start_time' => $session['start_time'] ?? null,
                'end_time' => $session['end_time'] ?? null,
                'distance' => $session['distance'] ?? 0,
                'duration_seconds' => $session['duration_seconds'] ?? 0,
                'mobile_user_id' => $mobileUserId,
                'status' => 'submitted',
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

    private function importEvents(array $events, string $extractPath, string $sessionId, ?string $mobileUserId): void
    {
        $targetAudioDir = public_path("activity-voice-notes/{$sessionId}");

        // Strict allowlist: filename MUST end in .m4a AND sniffed MIME must be a
        // plausible audio/MP4-container type. Both conditions required (AND, not OR) —
        // a filename-only check is trivially bypassable to upload arbitrary content.
        $allowedMimes = ['audio/mp4', 'audio/m4a', 'audio/x-m4a', 'audio/aac', 'video/mp4'];

        foreach ($events as $eventData) {
            $eventId = $eventData['id'];
            $audioFilename = $eventData['audioFilename'] ?? null;
            $audioDurationSeconds = $eventData['audioDurationSeconds'] ?? null;

            $voiceNotePath = null;
            if ($audioFilename) {
                $sourceAudio = $extractPath . '/audio/' . $audioFilename;
                $hasAllowedExtension = str_ends_with(strtolower($audioFilename), '.m4a');
                if ($hasAllowedExtension && file_exists($sourceAudio) && filesize($sourceAudio) <= 10 * 1024 * 1024) {
                    $mime = mime_content_type($sourceAudio);
                    if (in_array($mime, $allowedMimes, true)) {
                        if (!is_dir($targetAudioDir)) {
                            mkdir($targetAudioDir, 0777, true);
                        }
                        $targetAudio = $targetAudioDir . '/' . $audioFilename;
                        copy($sourceAudio, $targetAudio);
                        $voiceNotePath = "activity-voice-notes/{$sessionId}/{$audioFilename}";
                    }
                }
            }

            $existing = ActivityEvent::find($eventId);

            $updateData = [
                'session_id' => $sessionId,
                'mobile_user_id' => $mobileUserId,
                'title' => $this->sanitize($eventData['title'] ?? null),
                'description' => $this->sanitize($eventData['description'] ?? null),
                'operator_category' => $this->sanitize($eventData['operator_category'] ?? null),
                'latitude' => $eventData['latitude'],
                'longitude' => $eventData['longitude'],
                'timestamp' => $eventData['timestamp'],
                'status' => $existing ? $existing->status : 'submitted',
            ];

            if ($voiceNotePath) {
                // A fresh audio file was uploaded this sync — path and duration go together.
                $updateData['voice_note_path'] = $voiceNotePath;
                $updateData['voice_note_duration_seconds'] = $audioDurationSeconds;
            } elseif ($existing) {
                // No audio in this payload — preserve whatever was already stored,
                // don't let an incremental resync silently wipe the known duration.
                $updateData['voice_note_duration_seconds'] = $existing->voice_note_duration_seconds;
            }

            // ponytail: voice_note_transcription ceiling is manual reviewer form; sync MUST NOT overwrite existing transcription or transcribed_by
            if ($existing) {
                $updateData['voice_note_transcription'] = $existing->voice_note_transcription;
                $updateData['transcribed_by'] = $existing->transcribed_by;
            }

            ActivityEvent::updateOrCreate(
                ['id' => $eventId],
                $updateData
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
