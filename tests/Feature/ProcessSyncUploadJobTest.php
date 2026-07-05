<?php

namespace Tests\Feature;

use App\Jobs\ProcessSyncUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** TASK-213: ProcessSyncUpload job configuration test */
class ProcessSyncUploadJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_has_retry_configured(): void
    {
        $job = new ProcessSyncUpload('test-session-id', '/tmp/test.zip');
        $this->assertEquals(3, $job->tries);
    }

    public function test_job_implements_should_queue(): void
    {
        $job = new ProcessSyncUpload('test-session-id', '/tmp/test.zip');
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $job);
    }
}
