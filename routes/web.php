<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierMedicalController;
use App\Http\Controllers\CalendrierController;

// Page de connexion
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Routes d'authentification accessibles sans être connecté
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');

// Toutes les routes protégées par auth
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard/medecin', function () {
        return 'Bienvenue Médecin !';
    });

    Route::get('/dashboard/admin', function () {
        return 'Bienvenue Admin !';
    });

    // Espace secrétaire accessible aux secrétaires ET médecins
    Route::prefix('secretaire')->name('secretaire.')->middleware('role:secretaire|medecin')->group(function () {
        Route::get('/dashboard', [AuthController::class, 'secretDash'])->name('dashboard');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients');
        Route::get('/factures', [FactureController::class, 'index'])->name('factures');
        Route::get('/factures/print/{facture}', [FactureController::class, 'print'])->name('factures.print');
        Route::get('/rendezvous', [RendezvousController::class, 'index'])->name('rendezvous');
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements');
        Route::get('/docs', [DocumentController::class, 'index'])->name('docs');
        // Gestion prfl, 
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    });

    // Routes dossier médical et calendrier accessibles UNIQUEMENT au médecin
    Route::prefix('secretaire')->name('secretaire.')->middleware('role:medecin')->group(function () {
        Route::get('/dossier-medical', [DossierMedicalController::class, 'index'])->name('dossier-medical');
        Route::get('/calendrier', [CalendrierController::class, 'index'])->name('calendrier');

        // POST, PUT, DELETE dossier médical
        Route::post('/dossier-medical/consultation', [DossierMedicalController::class, 'storeConsultation'])->name('dossier-medical.consultation.store');
        Route::post('/dossier-medical/examen', [DossierMedicalController::class, 'storeExamenBiologique'])->name('dossier-medical.examen.store');
        Route::post('/dossier-medical/imagerie', [DossierMedicalController::class, 'storeImagerieMedicale'])->name('dossier-medical.imagerie.store');
        Route::post('/dossier-medical/vaccination', [DossierMedicalController::class, 'storeVaccination'])->name('dossier-medical.vaccination.store');
        Route::post('/dossier-medical/fichier', [DossierMedicalController::class, 'storeFichierMedical'])->name('dossier-medical.fichier.store');
        Route::post('/dossier-medical/habitude', [DossierMedicalController::class, 'storeHabitudeVie'])->name('dossier-medical.habitude.store');

        Route::delete('/dossier-medical/consultation', [DossierMedicalController::class, 'destroyConsultation'])->name('dossier-medical.consultation.destroy');
        Route::delete('/dossier-medical/examen', [DossierMedicalController::class, 'destroyExamenBiologique'])->name('dossier-medical.examen.destroy');
        Route::delete('/dossier-medical/imagerie', [DossierMedicalController::class, 'destroyImagerieMedicale'])->name('dossier-medical.imagerie.destroy');
        Route::delete('/dossier-medical/vaccination', [DossierMedicalController::class, 'destroyVaccination'])->name('dossier-medical.vaccination.destroy');
        Route::delete('/dossier-medical/fichier', [DossierMedicalController::class, 'destroyFichierMedical'])->name('dossier-medical.fichier.destroy');
        Route::delete('/dossier-medical/habitude', [DossierMedicalController::class, 'destroyHabitudeVie'])->name('dossier-medical.habitude.destroy');

        Route::put('/dossier-medical/consultation', [DossierMedicalController::class, 'updateConsultation'])->name('dossier-medical.consultation.update');
        Route::put('/dossier-medical/examen', [DossierMedicalController::class, 'updateExamenBiologique'])->name('dossier-medical.examen.update');
        Route::put('/dossier-medical/imagerie', [DossierMedicalController::class, 'updateImagerieMedicale'])->name('dossier-medical.imagerie.update');
        Route::put('/dossier-medical/vaccination', [DossierMedicalController::class, 'updateVaccination'])->name('dossier-medical.vaccination.update');
        Route::put('/dossier-medical/habitude', [DossierMedicalController::class, 'updateHabitudeVie'])->name('dossier-medical.habitude.update');
        Route::put('/dossier-medical/fichier', [DossierMedicalController::class, 'updateFichierMedical'])->name('dossier-medical.fichier.update');
    });

    // CRUD 
    Route::resource('rendezvous', RendezvousController::class)->except(['index']);
    Route::resource('patients', PatientController::class)->except(['index']);
    Route::resource('factures', FactureController::class)->except(['index']);
    Route::resource('paiements', PaiementController::class)->except(['index']);



    // Gestion des utilisateurs, admin uniquement
    Route::resource('users', UserController::class)->middleware('role:admin');
});
