<?php

use App\Http\Controllers\Apis\v1\AuthController;
use App\Http\Controllers\Apis\v1\GeneratorController;
use App\Http\Controllers\Apis\v1\HomeController;
use App\Http\Controllers\Apis\v1\HourRoundController;
use App\Http\Controllers\Apis\v1\InterventionController;
use App\Http\Controllers\Apis\v1\PhotoController;
use App\Http\Controllers\Apis\v1\TechnicalVisitController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'head'], '/ping', fn () => response()->json(['success' => true]));

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/home', [HomeController::class, 'index']);
        Route::get('/generators', [GeneratorController::class, 'index']);
        Route::get('/interventions', [InterventionController::class, 'index']);
        Route::post('/interventions/{id}/complete', [InterventionController::class, 'complete']);
        Route::post('/technical-visits', [TechnicalVisitController::class, 'store']);
        Route::post('/photos', [PhotoController::class, 'store']);
        Route::get('/hour-rounds', [HourRoundController::class, 'index']);
        Route::post('/hour-readings/{id}', [HourRoundController::class, 'storeReading']);
    });
});
