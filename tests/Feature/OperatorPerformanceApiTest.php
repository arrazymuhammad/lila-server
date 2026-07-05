<?php

namespace Tests\Feature;

use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OperatorPerformanceApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');
        $this->artisan('migrate');
    }

    public function test_returns_operator_ranking(): void
    {
        $u1 = User::create(['name' => 'Andi', 'email' => 'andi@lila.test', 'password' => 'secret123']);
        $u2 = User::create(['name' => 'Budi', 'email' => 'budi@lila.test', 'password' => 'secret123']);

        // user_id not in Fillable — insert directly
        DB::table('tracking_sessions')->insert([
            'id' => 'op-001',
            'user_id' => $u1->id,
            'status' => 'verified',
            'start_time' => now()->subDay(),
            'end_time' => now(),
            'distance' => 5000,
            'duration_seconds' => 3600,
        ]);
        DB::table('tracking_sessions')->insert([
            'id' => 'op-002',
            'user_id' => $u2->id,
            'status' => 'verified',
            'start_time' => now()->subDay(),
            'end_time' => now(),
            'distance' => 3000,
            'duration_seconds' => 1800,
        ]);

        $response = $this->getJson('/api/analytics/operators?days=7&limit=5');
        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.operator_name', 'Andi')
            ->assertJsonPath('data.0.total_distance', 5000)
            ->assertJsonPath('data.1.operator_name', 'Budi');
    }
}
