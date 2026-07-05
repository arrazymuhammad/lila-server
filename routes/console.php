<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// TASK-114: Clean temp directories older than 24h
Artisan::command('lila:clean-temp', function () {
    $tempDir = storage_path('app/temp');
    if (!is_dir($tempDir)) return;
    $now = time();
    foreach (glob($tempDir . '/*') as $dir) {
        if (is_dir($dir) && ($now - filemtime($dir)) > 86400) {
            \Illuminate\Support\Facades\Storage::disk('local')->deleteDirectory('temp/' . basename($dir));
        }
    }
    $this->info('Temp cleaned');
})->purpose('Clean temp extraction folders older than 24h');
