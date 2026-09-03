<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('pin');
            $table->string('auth_token', 64)->nullable()->index();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::table('tracking_sessions', function (Blueprint $table) {
            $table->uuid('mobile_user_id')->nullable()->index();
            $table->foreign('mobile_user_id')->references('id')->on('mobile_users')->onDelete('set null');
        });

        Schema::table('activity_events', function (Blueprint $table) {
            $table->uuid('mobile_user_id')->nullable()->index();
            $table->foreign('mobile_user_id')->references('id')->on('mobile_users')->onDelete('set null');
            $table->string('voice_note_path')->nullable();
            $table->integer('voice_note_duration_seconds')->nullable();
            $table->text('voice_note_transcription')->nullable();
            $table->unsignedBigInteger('transcribed_by')->nullable();
            $table->foreign('transcribed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('activity_events', function (Blueprint $table) {
            $table->dropForeign(['transcribed_by']);
            $table->dropForeign(['mobile_user_id']);
            $table->dropColumn([
                'mobile_user_id',
                'voice_note_path',
                'voice_note_duration_seconds',
                'voice_note_transcription',
                'transcribed_by'
            ]);
        });

        Schema::table('tracking_sessions', function (Blueprint $table) {
            $table->dropForeign(['mobile_user_id']);
            $table->dropColumn('mobile_user_id');
        });

        Schema::dropIfExists('mobile_users');
    }
};
