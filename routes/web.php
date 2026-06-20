<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
});

Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('activities', [ActivityController::class, 'index']);
Route::get('activities/{session}', [ActivityController::class, 'show']);
Route::get('findings', [FindingController::class, 'index']);
Route::get('findings/{event}', [FindingController::class, 'show']);
Route::get('map', [MapController::class, 'index']);
Route::get('maps', [MapController::class, 'index']);
