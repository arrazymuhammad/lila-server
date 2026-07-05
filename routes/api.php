<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SessionStatusController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::post('sync', [SyncController::class, 'activity']);
    Route::post('sessions/status', [SessionStatusController::class, 'check']);
    Route::get('categories', [CategoryController::class, 'index']); // TASK-118
});
