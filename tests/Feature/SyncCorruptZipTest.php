<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class SyncCorruptZipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Schema::hasTable('tracking_sessions')) {
            Schema::create('tracking_sessions', function ($table) {
                $table->string('id')->primary();
                $table->string('title')->nullable();
                $table->dateTime('start_time');
                $table->dateTime('end_time')->nullable();
                $table->float('distance')->default(0);
                $table->integer('duration_seconds')->default(0);
                $table->string('status')->default('draft');
            });
        }
    }

    public function test_rejects_corrupt_zip_file(): void
    {
        $corruptPath = storage_path('app/temp/corrupt_' . Str::uuid7() . '.zip');
        if (!is_dir(dirname($corruptPath))) {
            mkdir(dirname($corruptPath), 0777, true);
        }
        file_put_contents($corruptPath, 'this is not a valid zip archive content');

        $file = new UploadedFile($corruptPath, 'corrupt.zip', 'application/zip', null, true);
        $response = $this->postJson('/api/sync', ['file' => $file]);

        // Validation rejects non-zip content (422) before business logic (400)
        $response->assertStatus(422);

        @unlink($corruptPath);
    }
}
