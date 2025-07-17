<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PapierController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\OrdonnanceController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierMedicalController;
use App\Http\Controllers\CalendrierController;

// Page de connexion
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard/medecin', function () {
    return 'Bienvenue Médecin !';
});

Route::get('/medecin/certifDef', function () {
    $template = (object)[
        'logo_file_path' => 'cm_logo_default.png', // Make sure this file exists in public/uploads/ or public/storage/
        'nom_cabinet' => 'Cabinet du Dr. Dupont',
        'addr_cabinet' => '123 Rue de Paris, 75000 Paris',
        'tel_cabinet' => '01 23 45 67 89',
        'desc_cabinet' => 'Spécialiste en médecine générale',
    ];

    $patient_cin = 'AB123456';
    $patient_nom = 'Jean Martin';
    $date = now()->format('d/m/Y');
    $type = 'Consultation générale';
    $description = "Le patient présente des symptômes grippaux.\nRepos conseillé pendant 3 jours.";
    $medecin_nom = 'Dupont';

    return view('secretaire.templates.certifDefault', compact(
        'template', 'patient_cin', 'patient_nom', 'date', 'type', 'description', 'medecin_nom'
    ));
})->name('templates.certifDefault');

Route::get('/medecin/ordnnDef', function () {
    $template = (object)[
        'logo_file_path' => 'cm_logo_default.png', // Ensure this is in /public/uploads/ or /public/storage/
        'addr_cabinet' => '123 Rue de Lyon, 69000 Lyon',
        'tel_cabinet' => '04 56 78 90 12',
    ];

    $patient_cin = 'CD654321';
    $patient_nom = 'Marie Durand';
    $date = now()->format('d/m/Y');
    $medicaments = "Paracétamol 500mg - 3x/jour\nIbuprofène 200mg - 2x/jour";
    $instructions = "Boire beaucoup d'eau\nRepos complet";
    $duree = 5;
    $medecin_nom = 'Durand';

    return view('secretaire.templates.ordonnDefault', compact(
        'template', 'patient_cin', 'patient_nom', 'date', 'medicaments', 'instructions', 'duree', 'medecin_nom'
    ));
})->name('templates.ordonnDefault');


Route::get('/dashboard/admin', function () {
    return 'Bienvenue Admin !';
});

// Routes d'authentification
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');

// Routes secrétaire spécifiques
Route::get('/secretaire/dashboard', [AuthController::class, 'secretDash'])->name('secretaire.dashboard');
// Toutes les routes protégées par auth


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard/medecin', function () {
        return 'Bienvenue Médecin !';
    });

    Route::get('/dashboard/admin', function () {
        return 'Bienvenue Admin !';
    });


// Route pour la page des paiements (via controller)
// Route::get('/secretaire/paiements', [PaiementController::class, 'index'])->name('secretaire.paiements');

// // MODIFICATION ICI : Re-pointer vers le contrôleur DocumentController
// Route::get('/secretaire/certificats', [DocumentController::class, 'showCerts'])->name('secretaire.certificats');


// Route::get('/secretaire/ordonnances', [DocumentController::class, 'showOrds'])->name('secretaire.ordonnances');


    // Espace secrétaire accessible aux secrétaires ET médecins
    Route::prefix('secretaire')->name('secretaire.')->middleware('role:secretaire|medecin')->group(function () {
        Route::get('/dashboard', [AuthController::class, 'secretDash'])->name('dashboard');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients');
        Route::get('/factures', [FactureController::class, 'index'])->name('factures');
        Route::get('/factures/print/{facture}', [FactureController::class, 'print'])->name('factures.print');
        Route::get('/rendezvous', [RendezvousController::class, 'index'])->name('rendezvous');
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements');
        Route::get('/docs', [DocumentController::class, 'index'])->name('docs');
        Route::get('/papier', [PapierController::class, 'index'])->name('papier');
        // Gestion prfl, 
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::get('/remarques', [DocumentController::class, 'showRems'])->name('remarques');
    });

    // Routes dossier médical et calendrier accessibles UNIQUEMENT au médecin
    Route::prefix('secretaire')->name('secretaire.')->middleware('role:medecin')->group(function () {
        Route::get('/dossier-medical', [DossierMedicalController::class, 'index'])->name('dossier-medical');
        Route::get('/calendrier', [CalendrierController::class, 'index'])->name('calendrier');

        Route::get('/certificats', [DocumentController::class, 'showCerts'])->name('certificats');
        Route::get('/certificat/{id}', [CertificatController::class, 'show']);

        Route::get('/api/certificat/{id}/data', [CertificatController::class, 'getCertificatData']);
        Route::post('/certificat/store', [CertificatController::class, 'store'])->name('certificat.store');
        
        Route::get('/ordonnances', [DocumentController::class, 'showOrds'])->name('ordonnances');
        Route::post('/ordonnance/store', [OrdonnanceController::class, 'store'])->name('ordonnance.store');
        Route::get('/ordonnance/print/{id}', [OrdonnanceController::class, 'printOrdonnance'])->name('ordonnance.print');
        Route::get('/api/ordonnance/{id}/data', [OrdonnanceController::class, 'getOrdonnanceData']);


        // Add these routes to your existing routes
        Route::post('/papier/update-selection', [PapierController::class, 'updateSelection'])->name('papier.updateSelection');
        Route::delete('/papier/delete-template', [PapierController::class, 'deleteTemplate'])->name('papier.deleteTemplate');
        Route::get('/papier/get-template/{type}', [PapierController::class, 'getTemplate']);

        Route::post('/papier/store', [PapierController::class, 'store'])->name('papier.store');
        Route::post('/papier/createTemplate', [PapierController::class, 'createTemplate'])->name('papier.createTemplate');
        Route::post('/papier/updateTemplate', [PapierController::class, 'updateTemplate'])->name('papier.updateTemplate');
        Route::delete('papier/delete-template/{type}/{id}', [PapierController::class, 'deleteTemplate']);
        Route::get('/papier/template/{type}', [PapierController::class, 'getTemplates']);

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
