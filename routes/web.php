<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ApplicationController;

// Route::get('/', function () {
//     return view('welcome');
// });

// // Route::middleware('auth:sanctum')->group(function () {
//     // Route::apiResource('facilities', FacilityController::class);
//     // Route::apiResource('applications', ApplicationController::class);
// // });

// Route::apiResource('facilities', FacilityController::class);
// Route::post('applications', [ApplicationController::class, 'store']);
Route::prefix('api')->group(function () {
    Route::post('setFacilities', [FacilityController::class, 'store']);
    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('applications', ApplicationController::class);
    Route::post('setApplications', [ApplicationController::class, 'store']);
    Route::post('/send-whatsapp', [WhatsAppController::class, 'sendMessage']);

});

Route::get('/{any}', function () {
    return view('welcome'); // Ganti 'welcome' dengan nama file Blade utama Anda
})->where('any', '.*');
