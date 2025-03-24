<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\AuthController;

Route::prefix('api')->group(function () {
    Route::get('/host', [HostController::class,'getHost']);
    Route::post('setFacilities', [FacilityController::class, 'store']);
    Route::post('facilities/update/', [FacilityController::class, 'update']); // Use PUT method for update
    Route::delete('facilities/{facility}', [FacilityController::class, 'destroy']); // Use DELETE method for delete
    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('applications', ApplicationController::class);
    Route::post('setApplications', [ApplicationController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
});

Route::get('/{any}', function () {
    return view('welcome'); // Ganti 'welcome' dengan nama file Blade utama Anda
})->where('any', '.*');
