<?php

namespace Tests\Feature;

use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class VerificationTest extends TestCase
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
                $table->string('rejected_reason')->nullable();
            });
        }
        if (!Schema::hasTable('verification_audit_trails')) {
            Schema::create('verification_audit_trails', function ($table) {
                $table->id();
                $table->string('session_id');
                $table->string('event_id')->nullable();
                $table->string('action');
                $table->string('verifier_name');
                $table->text('reason')->nullable();
                $table->json('changes')->nullable();
                $table->dateTime('created_at')->useCurrent();
            });
        }
    }

    public function test_operator_can_verify_session(): void
    {
        $user = User::factory()->create();
        $session = TrackingSession::create([
            'id' => Str::uuid7()->toString(),
            'title' => 'Test Session',
            'start_time' => now(),
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($user)->patch("/verifications/{$session->id}/verify", [
            'action' => 'verify',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tracking_sessions', [
            'id' => $session->id,
            'status' => 'verified',
        ]);
        $this->assertDatabaseHas('verification_audit_trails', [
            'session_id' => $session->id,
            'action' => 'verify',
        ]);
    }

    public function test_operator_can_reject_session_with_reason(): void
    {
        $user = User::factory()->create();
        $session = TrackingSession::create([
            'id' => Str::uuid7()->toString(),
            'title' => 'Test Session',
            'start_time' => now(),
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($user)->patch("/verifications/{$session->id}/verify", [
            'action' => 'reject',
            'reason' => 'Foto tidak jelas',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tracking_sessions', [
            'id' => $session->id,
            'status' => 'rejected',
            'rejected_reason' => 'Foto tidak jelas',
        ]);
    }
}
