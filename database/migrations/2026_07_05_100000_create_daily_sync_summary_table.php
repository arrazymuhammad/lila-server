<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_sync_summary', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_sessions')->default(0);
            $table->float('total_distance')->default(0);
            $table->integer('total_duration')->default(0);
            $table->integer('verified_sessions')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sync_summary');
    }
};
