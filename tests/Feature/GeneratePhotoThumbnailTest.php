<?php

namespace Tests\Feature;

use App\Jobs\GeneratePhotoThumbnail;
use App\Models\ActivityPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/** TASK-214: GeneratePhotoThumbnail job test */
class GeneratePhotoThumbnailTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_handles_missing_file_gracefully(): void
    {
        Storage::fake('local');
        $pid = Str::uuid7()->toString();
        ActivityPhoto::create([
            'id' => $pid,
            'session_id' => 's1',
            'event_id' => 'e1',
            'file_path' => 'missing/photo.jpg',
            'filename' => 'photo.jpg',
        ]);

        $job = new GeneratePhotoThumbnail($pid);
        $job->handle();

        $photo = ActivityPhoto::find($pid);
        $this->assertNull($photo->thumbnail_path);
    }
}
