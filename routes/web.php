<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard/secretaire', function () {
    return 'Bienvenue Secrétaire !';
});

Route::get('/dashboard/medecin', function () {
    return 'Bienvenue Médecin !';
});

Route::get('/dashboard/admin', function () {
    return 'Bienvenue Admin !';
});

// Routes d'authentification
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');

// Routes secrétaire spécifiques
Route::get('/secretaire/dashboard', function () {
    return view('secretaire.dashboard');
})->name('secretaire.dashboard');

Route::get('/secretaire/rendezvous', [RendezvousController::class, 'index'])->name('secretaire.rendezvous');
Route::get('/secretaire/patients', function () {
    return view('secretaire.patients');
})->name('secretaire.patients');
Route::get('/secretaire/factures', function () {
    return view('secretaire.factures');
})->name('secretaire.factures');
Route::get('/secretaire/documents', function () {
    return view('secretaire.documents');
})->name('secretaire.documents');

// Routes resource pour les rendez-vous (avec noms personnalisés)
Route::post('/rendezvous', [RendezvousController::class, 'store'])->name('rendezvous.store');
Route::put('/rendezvous/{rendezvous}', [RendezvousController::class, 'update'])->name('rendezvous.update');
Route::delete('/rendezvous/{rendezvous}', [RendezvousController::class, 'destroy'])->name('rendezvous.destroy');
Route::get('/rendezvous/{rendezvous}', [RendezvousController::class, 'show'])->name('rendezvous.show');
Route::get('/rendezvous/{rendezvous}/edit', [RendezvousController::class, 'edit'])->name('rendezvous.edit');
Route::get('/rendezvous/create', [RendezvousController::class, 'create'])->name('rendezvous.create');

// Autres routes resource
Route::resource('users', UserController::class);
Route::resource('patients', PatientController::class);
Route::resource('factures', FactureController::class);
Route::resource('paiements', PaiementController::class);