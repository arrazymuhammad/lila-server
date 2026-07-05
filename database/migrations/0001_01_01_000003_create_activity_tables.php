<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->float('distance')->default(0);
            $table->integer('duration_seconds')->default(0);
            $table->string('status')->default('draft');
            $table->foreignId('user_id')->nullable()->index();
        });

        Schema::create('track_points', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->float('latitude');
            $table->float('longitude');
            $table->dateTime('timestamp');
        });

        Schema::create('activity_events', function (Blueprint $table) {
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

        Schema::create('activity_photos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('session_id');
            $table->string('event_id');
            $table->string('file_path');
            $table->string('filename');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->boolean('selected')->default(true);
            $table->string('thumbnail_path')->nullable();
        });

        Schema::create('finding_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finding_categories');
        Schema::dropIfExists('activity_photos');
        Schema::dropIfExists('activity_events');
        Schema::dropIfExists('track_points');
        Schema::dropIfExists('tracking_sessions');
    }
};
