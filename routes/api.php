<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login', [AuthController::class, 'apiLogin']);
//Route::middleware('auth:sanctum')->group(function () {


Route::apiResource('users', UserController::class);
Route::apiResource('patients', PatientController::class);
Route::apiResource('rendezvous', RendezvousController::class);
Route::apiResource('factures', FactureController::class);
Route::apiResource('paiements', PaiementController::class);


//secretaire




   // Route::post('/logout', [AuthController::class, 'logout']);
//});


