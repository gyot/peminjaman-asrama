<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\Profile;
use App\Http\Controllers\Api\EducationController;    
use App\Http\Controllers\Api\PositionController;

// AUTH
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Rate limit login
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// PUBLIC ROUTES

// PROTECTED ROUTES
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::get('/host', [HostController::class, 'getHost']);

    // Applications
    Route::apiResource('applications', ApplicationController::class)->except(['edit', 'create']);
    Route::get('applications/{id}/detail/{approvalStatus}', [ApplicationController::class, 'detail']); // Custom detail route
    Route::get('approvals', [ApplicationController::class, 'approvals']);

    // Facilities
    Route::apiResource('facilities', FacilityController::class)->except(['edit', 'create']);

    // Profiles
    Route::get('profile/{id}', [Profile::class, 'getProfile']);
    Route::post('update/profiles', [Profile::class, 'updateProfile']);

    // Positions
    // Route::get('/positions', [PositionController::class, 'index']);
    // Route::post('/positions', [PositionController::class, 'store']);
    // Route::put('/positions/{id}', [PositionController::class, 'update']); // now using PUT method
    // Route::delete('/positions/{id}', [PositionController::class, 'destroy']);

    // Educations
    Route::get('/educations', [EducationController::class, 'index']);
    Route::post('/educations', [EducationController::class, 'store']);
    Route::put('/educations/{id}', [EducationController::class, 'update']); // now using PUT method
    Route::delete('/educations/{id}', [EducationController::class, 'destroy']);
});
// Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::get('/{any}', function () {
    return view('welcome'); // Ensure 'welcome' Blade file exists or replace with the correct file name
})->where('any', '.*');
