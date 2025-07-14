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

// Page de connexion
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Routes d'authentification accessibles sans être connecté
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');

// 🔐 Toutes les routes sécurisées par "auth"
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/medecin', function () {
        return 'Bienvenue Médecin !';
    });
    Route::get('/dashboard/admin', function () {
        return 'Bienvenue Admin !';
    });

    // Routes secrétaire spécifiques
    Route::get('/secretaire/dashboard', [AuthController::class, 'secretDash'])->name('secretaire.dashboard');
    Route::get('/secretaire/rendezvous', [RendezvousController::class, 'index'])->name('secretaire.rendezvous');
    Route::get('/secretaire/patients', [PatientController::class, 'index'])->name('secretaire.patients');
    Route::get('/secretaire/factures', [FactureController::class, 'index'])->name('secretaire.factures');
    Route::get('/secretaire/paiements', [PaiementController::class, 'index'])->name('secretaire.paiements');
    Route::get('/secretaire/docs', [DocumentController::class, 'index'])->name('secretaire.docs');

    // CRUD Rendez-vous
    Route::post('/rendezvous', [RendezvousController::class, 'store'])->name('rendezvous.store');
    Route::put('/rendezvous/{rendezvous}', [RendezvousController::class, 'update'])->name('rendezvous.update');
    Route::delete('/rendezvous/{rendezvous}', [RendezvousController::class, 'destroy'])->name('rendezvous.destroy');
    Route::get('/rendezvous/{rendezvous}', [RendezvousController::class, 'show'])->name('rendezvous.show');
    Route::get('/rendezvous/{rendezvous}/edit', [RendezvousController::class, 'edit'])->name('rendezvous.edit');
    Route::get('/rendezvous/create', [RendezvousController::class, 'create'])->name('rendezvous.create');

    // CRUD Patients
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');

    // Routes resource
    Route::resource('users', UserController::class);
    Route::resource('factures', FactureController::class);
    Route::resource('paiements', PaiementController::class);

    // Dossier médical
    Route::get('/secretaire/dossier-medical', [DossierMedicalController::class, 'index'])->name('secretaire.dossier-medical');
    Route::post('/secretaire/dossier-medical/consultation', [DossierMedicalController::class, 'storeConsultation'])->name('secretaire.dossier-medical.consultation.store');
    Route::post('/secretaire/dossier-medical/examen', [DossierMedicalController::class, 'storeExamenBiologique'])->name('secretaire.dossier-medical.examen.store');
    Route::post('/secretaire/dossier-medical/imagerie', [DossierMedicalController::class, 'storeImagerieMedicale'])->name('secretaire.dossier-medical.imagerie.store');
    Route::post('/secretaire/dossier-medical/vaccination', [DossierMedicalController::class, 'storeVaccination'])->name('secretaire.dossier-medical.vaccination.store');
    Route::post('/secretaire/dossier-medical/fichier', [DossierMedicalController::class, 'storeFichierMedical'])->name('secretaire.dossier-medical.fichier.store');
    Route::post('/secretaire/dossier-medical/habitude', [DossierMedicalController::class, 'storeHabitudeVie'])->name('secretaire.dossier-medical.habitude.store');


    // Suppression données médicales
    Route::delete('/secretaire/dossier-medical/consultation', [DossierMedicalController::class, 'destroyConsultation'])->name('secretaire.dossier-medical.consultation.destroy');
    Route::delete('/secretaire/dossier-medical/examen', [DossierMedicalController::class, 'destroyExamenBiologique'])->name('secretaire.dossier-medical.examen.destroy');
    Route::delete('/secretaire/dossier-medical/imagerie', [DossierMedicalController::class, 'destroyImagerieMedicale'])->name('secretaire.dossier-medical.imagerie.destroy');
    Route::delete('/secretaire/dossier-medical/vaccination', [DossierMedicalController::class, 'destroyVaccination'])->name('secretaire.dossier-medical.vaccination.destroy');
    Route::delete('/secretaire/dossier-medical/fichier', [DossierMedicalController::class, 'destroyFichierMedical'])->name('secretaire.dossier-medical.fichier.destroy');
    Route::delete('/secretaire/dossier-medical/habitude', [DossierMedicalController::class, 'destroyHabitudeVie'])->name('secretaire.dossier-medical.habitude.destroy');

    // Routes de modification des données médicales
    Route::put('/secretaire/dossier-medical/consultation', [DossierMedicalController::class, 'updateConsultation'])->name('secretaire.dossier-medical.consultation.update');
    Route::put('/secretaire/dossier-medical/examen', [DossierMedicalController::class, 'updateExamenBiologique'])->name('secretaire.dossier-medical.examen.update');
    Route::put('/secretaire/dossier-medical/imagerie', [DossierMedicalController::class, 'updateImagerieMedicale'])->name('secretaire.dossier-medical.imagerie.update');
    Route::put('/secretaire/dossier-medical/vaccination', [DossierMedicalController::class, 'updateVaccination'])->name('secretaire.dossier-medical.vaccination.update');
    Route::put('/secretaire/dossier-medical/habitude', [DossierMedicalController::class, 'updateHabitudeVie'])->name('secretaire.dossier-medical.habitude.update');
    Route::put('/secretaire/dossier-medical/fichier', [DossierMedicalController::class, 'updateFichierMedical'])->name('secretaire.dossier-medical.fichier.update'); // <-- Cette ligne a été ajoutée
});