<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SessionStatusController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::post('sync', [SyncController::class, 'activity']);
    Route::post('sessions/status', [SessionStatusController::class, 'check']);
    Route::get('categories', [CategoryController::class, 'index']); // TASK-118
    Route::get('analytics/trends', [\App\Http\Controllers\Api\AnalyticsController::class, 'trends']); // TASK-209
    Route::get('analytics/category-trends', [\App\Http\Controllers\Api\CategoryTrendController::class, 'index']); // TASK-203
    Route::get('analytics/heatmap', [\App\Http\Controllers\Api\HeatmapController::class, 'index']); // TASK-204
    Route::get('sync/status/{id}', [\App\Http\Controllers\Api\SyncStatusDetailController::class, 'show']); // TASK-238
});
