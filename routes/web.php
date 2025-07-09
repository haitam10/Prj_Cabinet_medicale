<?php

use App\Http\Controllers\OrdonnanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RemarqueController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('users', UserController::class);
Route::resource('patients', PatientController::class);
Route::resource('ordonnances', OrdonnanceController::class);
Route::resource('remarques', RemarqueController::class);
Route::resource('certificats', RemarqueController::class);