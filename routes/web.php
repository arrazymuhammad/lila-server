<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
});

Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('activities', [ActivityController::class, 'index']);
Route::get('activities/{session}', [ActivityController::class, 'show']);
