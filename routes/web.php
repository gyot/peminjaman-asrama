<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\Profile;
use App\Http\Controllers\Api\EducationController;    
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\TrainingHistoryController;

// AUTH
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Rate limit login
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// PUBLIC ROUTES
Route::get('get/facilities', [FacilityController::class,'index']);
// PROTECTED ROUTES
    Route::get('data_diri/{id}', [DataController::class,'show']);
Route::get('/host', [HostController::class, 'getHost']);
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    
    // Applications
    Route::apiResource('applications', ApplicationController::class)->except(['edit', 'create']);
    Route::get('applications/{id}/detail/{approvalStatus}', [ApplicationController::class, 'detail']); // Custom detail route
    Route::get('approvals', [ApplicationController::class, 'approvals']);
    
     // Applications
    
    // Facilities
    // Route::apiResource('facilities', [FacilityController::class,'index'])->only(['index']);
    // Route::get('facilities/{id}/detail', [FacilityController::class,'show']);
    // Route::get('facilities/{id}/edit', [FacilityController::class,'show']);
    Route::get('facilities', [FacilityController::class,'index']);
    Route::post('setFacilities/', [FacilityController::class,'store']);
    Route::put('facilities/{id}', [FacilityController::class,'update']);
    Route::delete('facilities/{facility}', [FacilityController::class,'destroy']);
    // Route::get('facilities/{id}', [FacilityController::class,'show']);

    // Profiles
    // Route::get('profile/{id}', [Profile::class, 'getProfile']);
    Route::put('profile/profiles/{id}', [Profile::class, 'updateProfile']);
    Route::post('profile', [Profile::class, 'store']);
    Route::get('profile/{id}', [Profile::class, 'show']);
    Route::get('cv/{id}', [Profile::class, 'showProfile']);

    // Positions
    Route::get('/positions', [PositionController::class, 'index']);
    Route::post('/positions', [PositionController::class, 'store']);
    Route::put('/positions/{id}', [PositionController::class, 'update']); // now using PUT method
    Route::delete('/positions/{id}', [PositionController::class, 'destroy']);

    // Educations
    Route::get('/educations', [EducationController::class, 'index']);
    Route::post('/educations', [EducationController::class, 'store']);
    Route::put('/educations/{id}', [EducationController::class, 'update']); // now using PUT method
    Route::delete('/educations/{id}', [EducationController::class, 'destroy']);

    // Account
    // Route::get('/account', [Profile::class, 'me']);
    Route::put('/account/{id}', [Profile::class, 'updateAccount']);

    // Training History
    Route::get('/training-histories', [TrainingHistoryController::class, 'index']);
    Route::post('/training-histories', [TrainingHistoryController::class, 'store']);
    Route::get('/training-histories/{id}', [TrainingHistoryController::class, 'show']);
    Route::put('/training-histories/{id}', [TrainingHistoryController::class, 'update']);
    Route::delete('/training-histories/{id}', [TrainingHistoryController::class, 'destroy']);
});
// Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::get('/{any}', function () {
    return view('welcome'); // Ensure 'welcome' Blade file exists or replace with the correct file name
})->where('any', '.*');
