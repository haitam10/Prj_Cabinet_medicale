<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;

Route::apiResource('users', UserController::class);
Route::apiResource('patients', PatientController::class);
