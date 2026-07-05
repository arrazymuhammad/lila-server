<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_events', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('status');
        });

        Schema::table('activity_photos', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::table('activity_events', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('activity_photos', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['event_id']);
        });
    }
};
