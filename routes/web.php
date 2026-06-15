<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('activities', [ActivityController::class, 'index']);
Route::get('activities/{session}', [ActivityController::class, 'show']);
