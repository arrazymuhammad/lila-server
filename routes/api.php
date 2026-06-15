<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::get('sync', function(){
    return "hehhe";
});

Route::post('sync', [SyncController::class, 'activity']);
