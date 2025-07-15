<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController; // Assurez-vous que cette ligne est présente
use App\Http\Controllers\PapierController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\OrdonnanceController;

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

// Route pour la page des rendez-vous (via controller)
Route::get('/secretaire/rendezvous', [RendezvousController::class, 'index'])->name('secretaire.rendezvous');

// Route pour la page des patients (via controller)
Route::get('/secretaire/patients', [PatientController::class, 'index'])->name('secretaire.patients');

// Route pour la page des factures (via controller)
Route::get('/secretaire/factures', [FactureController::class, 'index'])->name('secretaire.factures');

// Route pour la page des paiements (via controller)
Route::get('/secretaire/paiements', [PaiementController::class, 'index'])->name('secretaire.paiements');

// MODIFICATION ICI : Re-pointer vers le contrôleur DocumentController
Route::get('/secretaire/certificats', [DocumentController::class, 'showCerts'])->name('secretaire.certificats');


Route::get('/secretaire/ordonnances', [DocumentController::class, 'showOrds'])->name('secretaire.ordonnances');


Route::get('/secretaire/remarques', [DocumentController::class, 'showRems'])->name('secretaire.remarques');


Route::get('/medecin/papier', [PapierController::class, 'index'])->name('secretaire.papier');



// Routes CRUD pour les rendez-vous
Route::post('/rendezvous', [RendezvousController::class, 'store'])->name('rendezvous.store');
Route::put('/rendezvous/{rendezvous}', [RendezvousController::class, 'update'])->name('rendezvous.update');
Route::delete('/rendezvous/{rendezvous}', [RendezvousController::class, 'destroy'])->name('rendezvous.destroy');
Route::get('/rendezvous/{rendezvous}', [RendezvousController::class, 'show'])->name('rendezvous.show');
Route::get('/rendezvous/{rendezvous}/edit', [RendezvousController::class, 'edit'])->name('rendezvous.edit');
Route::get('/rendezvous/create', [RendezvousController::class, 'create'])->name('rendezvous.create');


// Routes CRUD pour les patients
Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');



Route::post('/certificat/store', [CertificatController::class, 'store'])->name('certificat.store');
Route::post('/ordonnance/store', [OrdonnanceController::class, 'store'])->name('ordonnance.store');

// Routes resource pour les autres entités
Route::resource('users', UserController::class);
Route::resource('factures', FactureController::class);
Route::resource('paiements', PaiementController::class);
