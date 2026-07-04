<?php

namespace Tests\Feature;

use App\Models\TrackingSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class SyncIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('tracking_sessions', function ($table) {
            $table->string('id')->primary();
            $table->string('title')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->float('distance')->default(0);
            $table->integer('duration_seconds')->default(0);
            $table->string('status')->default('draft');
        });

        Schema::create('track_points', function ($table) {
            $table->string('id')->primary()->autoIncrement();
            $table->string('session_id');
            $table->float('latitude');
            $table->float('longitude');
            $table->dateTime('timestamp');
        });

        Schema::create('activity_events', function ($table) {
            $table->string('id')->primary();
            $table->string('session_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->float('latitude');
            $table->float('longitude');
            $table->dateTime('timestamp');
            $table->string('status')->default('draft');
            $table->string('operator_category')->nullable();
        });

        Schema::create('activity_photos', function ($table) {
            $table->string('id')->primary();
            $table->string('session_id');
            $table->string('event_id');
            $table->string('file_path');
            $table->string('filename');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->boolean('selected')->default(true);
        });
    }

    private function createDummyZip(string $sessionId): string
    {
        $zipPath = storage_path('app/temp/test_' . Str::uuid7() . '.zip');
        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0777, true);
        }

        $metadata = [
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'session' => [
                'id' => $sessionId,
                'title' => 'Test Session',
                'start_time' => now()->toIso8601String(),
                'distance' => 100.0,
                'duration_seconds' => 60,
            ],
            'track_points' => [],
            'events' => [],
            'photos' => [],
        ];

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE);
        $zip->addFromString('metadata.json', json_encode($metadata));
        $zip->close();

        return $zipPath;
    }

    public function test_sync_rejects_verified_session(): void
    {
        $sessionId = Str::uuid7()->toString();

        TrackingSession::create([
            'id' => $sessionId,
            'title' => 'Original Verified',
            'start_time' => now(),
            'distance' => 50.0,
            'duration_seconds' => 30,
            'status' => 'verified',
        ]);

        $zipPath = $this->createDummyZip($sessionId);
        $file = new UploadedFile($zipPath, 'test.zip', 'application/zip', null, true);

        $response = $this->postJson('/api/sync', ['file' => $file]);

        $response->assertStatus(409);
        $response->assertJson(['message' => 'Sesi sudah terverifikasi. Tidak dapat melakukan sinkronisasi ulang.']);

        $this->assertDatabaseHas('tracking_sessions', [
            'id' => $sessionId,
            'title' => 'Original Verified',
            'status' => 'verified',
        ]);

        @unlink($zipPath);
    }

    public function test_sync_upserts_submitted_session(): void
    {
        $sessionId = Str::uuid7()->toString();

        TrackingSession::create([
            'id' => $sessionId,
            'title' => 'Old Title',
            'start_time' => now(),
            'distance' => 50.0,
            'duration_seconds' => 30,
            'status' => 'submitted',
        ]);

        $zipPath = $this->createDummyZip($sessionId);
        $file = new UploadedFile($zipPath, 'test.zip', 'application/zip', null, true);

        $response = $this->postJson('/api/sync', ['file' => $file]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tracking_sessions', [
            'id' => $sessionId,
            'title' => 'Test Session',
            'status' => 'submitted',
        ]);

        @unlink($zipPath);
    }
}
