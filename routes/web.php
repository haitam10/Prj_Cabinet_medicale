<?php

use App\Http\Controllers\FactureController;
use App\Http\Controllers\OrdonnanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RemarqueController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\SecretaireController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('users', UserController::class);
Route::resource('patients', PatientController::class);
Route::resource('ordonnances', OrdonnanceController::class);
Route::resource('remarques', RemarqueController::class);
Route::resource('certificats', RemarqueController::class);


Route::get('secretaire/', [SecretaireController::class, 'Home'])->name('secretaire.dash');

// Route::get('secretaire/rdv/store', [RendezvousController::class, 'rdvDispo'])->name('secretaire.rdvDispo');
Route::post('secretaire/patient/store', [PatientController::class, 'store'])->name('secretaire.patientStore');
Route::post('secretaire/facture/store', [FactureController::class, 'factureStore'])->name('secretaire.factureStore');
Route::get('secretaire/facture/imprimer/{}', [FactureController::class, 'factureImprim'])->name('secretaire.factureImprim');

Route::get('secretaire/getMedsDispo', [RendezvousController::class, 'rdvDispo'])->name('secretaire.rdvDispo');




