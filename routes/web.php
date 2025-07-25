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
use App\Http\Controllers\PapierController;
use App\Http\Controllers\OrdonnanceController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\RemarqueController;


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
        Route::get('/patients/{patient}/details', [PatientController::class, 'getPatientDetails'])->name('patients.details');
        Route::get('/factures', [FactureController::class, 'index'])->name('factures');
        Route::get('/factures/print/{facture}', [FactureController::class, 'print'])->name('factures.print');
        Route::get('/rendezvous', [RendezvousController::class, 'index'])->name('rendezvous');
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements');
        Route::get('/docs', [DocumentController::class, 'index'])->name('docs');

        // Gestion profil
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

        // Routes rendez-vous - accessibles aux secrétaires ET médecins
        Route::post('/rendezvous', [RendezvousController::class, 'store'])->name('rendezvous.store');
        Route::get('/rendezvous/create', [RendezvousController::class, 'create'])->name('rendezvous.create');
        Route::get('/rendezvous/{rendezvous}', [RendezvousController::class, 'show'])->name('rendezvous.show');
        Route::get('/rendezvous/{rendezvous}/edit', [RendezvousController::class, 'edit'])->name('rendezvous.edit');
        Route::put('/rendezvous/{rendezvous}', [RendezvousController::class, 'update'])->name('rendezvous.update');
        Route::delete('/rendezvous/{rendezvous}', [RendezvousController::class, 'destroy'])->name('rendezvous.destroy');

        // Routes disponibilités - AJOUTEZ CES LIGNES
        Route::post('/disponibilite', [RendezvousController::class, 'storeDisponibilite'])->name('disponibilite.store');
        Route::put('/disponibilite/{id}', [RendezvousController::class, 'updateDisponibilite'])->name('disponibilite.update');
        Route::delete('/disponibilite/{id}', [RendezvousController::class, 'destroyDisponibilite'])->name('disponibilite.destroy');
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

        // Routes pour certificats
        Route::post('/dossier-medical/certificat', [DossierMedicalController::class, 'storeCertificat'])->name('dossier-medical.certificat.store');
        Route::put('/dossier-medical/certificat', [DossierMedicalController::class, 'updateCertificat'])->name('dossier-medical.certificat.update');
        Route::delete('/dossier-medical/certificat', [DossierMedicalController::class, 'destroyCertificat'])->name('dossier-medical.certificat.destroy');

        // Routes pour remarques
        Route::post('/dossier-medical/remarque', [DossierMedicalController::class, 'storeRemarque'])->name('dossier-medical.remarque.store');
        Route::put('/dossier-medical/remarque', [DossierMedicalController::class, 'updateRemarque'])->name('dossier-medical.remarque.update');
        Route::delete('/dossier-medical/remarque', [DossierMedicalController::class, 'destroyRemarque'])->name('dossier-medical.remarque.destroy');

        // Routes certificats
        Route::get('/certificats', [DocumentController::class, 'showCerts'])->name('certificats');
        Route::get('/certificat/{id}', [CertificatController::class, 'show'])->name('certificat.show');
        Route::post('/certificat/store', [CertificatController::class, 'store'])->name('certificat.store');

        // Routes ordonnances
        Route::get('/ordonnances', [DocumentController::class, 'showOrds'])->name('ordonnances');
        Route::post('/ordonnance/store', [OrdonnanceController::class, 'store'])->name('ordonnance.store');
        Route::get('/ordonnance/print/{id}', [OrdonnanceController::class, 'printOrdonnance'])->name('ordonnance.print');

        // Routes remarques
        Route::get('/remarques', [DocumentController::class, 'showRems'])->name('remarques');
        Route::post('/remarques', [RemarqueController::class, 'store'])->name('remarques.store');
        Route::put('/remarques/{id}', [RemarqueController::class, 'update'])->name('remarques.update');
        Route::delete('/remarques/{id}', [RemarqueController::class, 'destroy'])->name('remarques.destroy');

        // Routes papier
        Route::get('/papier', [PapierController::class, 'index'])->name('papier');
        Route::post('/papier/update-selection', [PapierController::class, 'updateSelection'])->name('papier.updateSelection');
        Route::delete('/papier/delete-template', [PapierController::class, 'deleteTemplate'])->name('papier.deleteTemplate');
        Route::get('/papier/get-template/{type}/{id}', [PapierController::class, 'getTemplate'])->name('papier.getTemplate');
        Route::post('/papier/store', [PapierController::class, 'store'])->name('papier.store');
        Route::post('/papier/createTemplate', [PapierController::class, 'createTemplate'])->name('papier.createTemplate');
        Route::post('/papier/updateTemplate', [PapierController::class, 'updateTemplate'])->name('papier.updateTemplate');
        Route::delete('papier/delete-template/{type}/{id}', [PapierController::class, 'deleteTemplate']);
        Route::get('papier/template/{type}', [PapierController::class, 'getTemplates']);

        // Routes secrétaires management
        Route::get('/papier/available-secretaires', [PapierController::class, 'getAvailableSecretaires'])->name('papier.getAvailableSecretaires');
        Route::post('/papier/associate-secretaire', [PapierController::class, 'associateSecretaire'])->name('papier.associateSecretaire');
        Route::delete('/papier/dissociate-secretaire/{secretaireId}', [PapierController::class, 'dissociateSecretaire'])->name('papier.dissociateSecretaire');
    });

    // Routes API pour les certificats et ordonnances - accessibles aux médecins uniquement
    Route::middleware('role:medecin')->group(function () {
        Route::get('/api/certificat/{id}/data', [CertificatController::class, 'getCertificatData']);
        Route::get('/api/ordonnance/{id}/data', [OrdonnanceController::class, 'getOrdonnanceData']);
    });

    // pour maintenir les noms de routes et URLs originaux
    Route::resource('patients', PatientController::class)->except(['index']);
    Route::resource('factures', FactureController::class)->except(['index']);
    Route::resource('paiements', PaiementController::class)->except(['index']);

    // Gestion des utilisateurs, admin uniquement
    Route::resource('users', UserController::class)->middleware('role:admin');
});
