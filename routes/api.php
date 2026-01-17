<?php

use App\Http\Controllers\OrdonnanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RemarqueController;
use App\Http\Controllers\CertificatController;

Route::apiResource('users', UserController::class);
Route::apiResource('patients', PatientController::class);
Route::apiResource('ordonnances', OrdonnanceController::class);
Route::apiResource('remarques', RemarqueController::class);
Route::apiResource('certificats', RemarqueController::class);
Route::get('ordonnance/{id}/data', [OrdonnanceController::class, 'getOrdonnanceData']);
Route::get('certificat/{id}/data', [CertificatController::class, 'getCertificatData']);




   // Route::post('/logout', [AuthController::class, 'logout']);
//});


