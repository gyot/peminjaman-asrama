<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\Profile;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\PositionController;

// Contoh route API
Route::post('/setApplications', [ApplicationController::class, 'store']);
Route::post('applications/{id}/approval', [ApplicationController::class, 'setApproval']);
    // Educations
    Route::get('/positions', [PositionController::class, 'index']);
    Route::post('/positions', [PositionController::class, 'store']);
    Route::put('/positions/{id}', [PositionController::class, 'update']); // now using PUT method
    Route::delete('/positions/{id}', [PositionController::class, 'destroy']);
    // Route::get('/profile/{id}', [Profile::class, 'getProfile']);
    // Route::get('facilitiess', [FacilityController::class,'index']);