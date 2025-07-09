<?php

use App\Http\Controllers\OrdonnanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RemarqueController;

Route::apiResource('users', UserController::class);
Route::apiResource('patients', PatientController::class);
Route::apiResource('ordonnances', OrdonnanceController::class);
Route::apiResource('remarques', RemarqueController::class);
Route::apiResource('certificats', RemarqueController::class);



