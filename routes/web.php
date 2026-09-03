<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TASK-169: Override health check with detailed database diagnosis
Route::get('/up', \App\Http\Controllers\HealthController::class);

Route::get('login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('guest');
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('activities', [ActivityController::class, 'index']);
    Route::get('activities/{session}', [ActivityController::class, 'show']);

    Route::get('verifications', [VerificationController::class, 'index']);
    Route::patch('verifications/{session}/verify', [VerificationController::class, 'verify'])->name('verifications.verify');
    Route::post('verifications/bulk-verify', [VerificationController::class, 'bulkVerify'])->name('verifications.bulk-verify');

    Route::get('export/sessions', [ExportController::class, 'sessions'])->name('export.sessions');
    Route::get('export/findings', [ExportController::class, 'findings'])->name('export.findings');

    Route::prefix('verifications/findings')->group(function () {
        Route::get('/', [\App\Http\Controllers\Verification\FindingController::class, 'index']);
    });

    Route::prefix('verifications/sessions/{session}/findings')->group(function () {
        Route::get('review', [\App\Http\Controllers\Verification\FindingController::class, 'review'])->name('verifications.findings.review');
        Route::patch('{event}/verify', [\App\Http\Controllers\Verification\FindingController::class, 'verify'])->name('verifications.findings.verify');
    });

    Route::get('findings', [FindingController::class, 'index']);
    Route::get('findings/{event}', [FindingController::class, 'show']);
    Route::get('map', [MapController::class, 'index']);
    Route::get('maps', [MapController::class, 'index']);

    Route::resource('categories', \App\Http\Controllers\FindingCategoryController::class)->only(['index', 'store', 'destroy']);

    Route::get('mobile-users', [\App\Http\Controllers\MobileUserController::class, 'index'])->name('mobile-users.index');
    Route::patch('mobile-users/{mobileUser}/toggle-active', [\App\Http\Controllers\MobileUserController::class, 'toggleActive'])->name('mobile-users.toggle-active');
});
