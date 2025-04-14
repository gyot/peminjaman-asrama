<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\AuthController;

Route::prefix('api')->group(function () {
    Route::get('/host', [HostController::class, 'getHost']);
    Route::get('/applications/update/{id}', [ApplicationController::class, 'update']);
    Route::post('setFacilities', [FacilityController::class, 'store']);
    Route::put('facilities/update/', [FacilityController::class, 'update']); // Changed to PUT method
    Route::delete('facilities/{facility}', [FacilityController::class, 'destroy']);
    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('applications', ApplicationController::class);
    Route::get('applications/{id}', [ApplicationController::class, 'show']);
    Route::get('applications/{id}/detail/{approvalStatus}', [ApplicationController::class, 'show']);
    // Route::post('applications/{id}/approval', [ApplicationController::class, 'setApproval']);
    // Route::post('setApplications', [ApplicationController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
    Route::get('approvals/', [ApplicationController::class, 'approvals']);
    
    // Route::get('rejection/{id}', [ApplicationController::class, 'rejection']);
});

Route::get('/{any}', function () {
    return view('welcome'); // Ensure 'welcome' Blade file exists or replace with the correct file name
})->where('any', '.*');
