<?php

use App\Http\Controllers\Apis\v1\AuthController;
use App\Http\Controllers\Apis\v1\HomeController;
use App\Http\Controllers\Apis\v1\InterventionController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/home', [HomeController::class, 'index']);
        Route::get('/interventions', [InterventionController::class, 'index']);
    });
});

