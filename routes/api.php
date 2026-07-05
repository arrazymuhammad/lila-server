<?php

use App\Http\Controllers\Api\SessionStatusController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::post('sync', [SyncController::class, 'activity']);
Route::post('sessions/status', [SessionStatusController::class, 'check']);
