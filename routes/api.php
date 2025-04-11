<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ApplicationController;

// Contoh route API
Route::post('/setApplications', [ApplicationController::class, 'store']);
Route::post('applications/{id}/approval', [ApplicationController::class, 'setApproval']);