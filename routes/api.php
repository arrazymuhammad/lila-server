<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SessionStatusController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\MobileAuthController;
use Illuminate\Support\Facades\Route;

// Auth Routes (Unguarded by X-Sync-Token)
Route::prefix('auth')->group(function () {
    Route::middleware('throttle:20,1')->post('register', [MobileAuthController::class, 'register']);
    // Tighter throttle on login — it's a PIN-guess surface (6 digits = 1,000,000 combos),
    // so brute-force attempts must be slowed far more than routine registration traffic.
    Route::middleware('throttle:5,1')->post('login', [MobileAuthController::class, 'login']);
});

Route::middleware('throttle:60,1')->group(function () {
    // Sync family routes (Guarded by shared sync_token and resolves mobile_user_token)
    Route::middleware(['sync_token', 'mobile_user_token'])->group(function () {
        Route::middleware('throttle:10,1')->group(function () {
            Route::post('sync', [SyncController::class, 'activity']);
        });
        Route::post('sessions/status', [SessionStatusController::class, 'check']);
        Route::get('sync/status/{id}', [\App\Http\Controllers\Api\SyncStatusDetailController::class, 'show']); // TASK-238
    });

    Route::middleware('sync_token')->group(function () {
        Route::get('categories', [CategoryController::class, 'index']); // TASK-118
        Route::get('analytics/trends', [\App\Http\Controllers\Api\AnalyticsController::class, 'trends']); // TASK-209
        Route::get('analytics/category-trends', [\App\Http\Controllers\Api\CategoryTrendController::class, 'index']); // TASK-203
        Route::get('analytics/heatmap', [\App\Http\Controllers\Api\HeatmapController::class, 'index']); // TASK-204
        Route::get('analytics/operators', [\App\Http\Controllers\Api\OperatorPerformanceController::class, 'index']); // TASK-205
    });
});
