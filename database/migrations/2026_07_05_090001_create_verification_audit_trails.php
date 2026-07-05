<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('event_id')->nullable()->index();
            $table->string('action'); // verify | reject
            $table->string('verifier_name')->nullable();
            $table->text('reason')->nullable();
            $table->json('changes')->nullable(); // snapshot of what changed
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_audit_trails');
    }
};
