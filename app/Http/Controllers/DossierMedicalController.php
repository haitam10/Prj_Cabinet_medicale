<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\ExamenBiologique;
use App\Models\FichierMedical;
use App\Models\ImagerieMedicale;
use App\Models\Vaccination;
use App\Models\HabitudeVie;
use App\Models\Certificat;
use App\Models\Remarque;
use App\Models\Rendezvous;
use App\Models\Ordonnance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DossierMedicalController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $currentUser = Auth::user();
        $isCurrentUserMedecin = $currentUser && $currentUser->role === 'medecin';

        // Récupérer les patients avec rendez-vous pour le médecin connecté
        $patientsAvecRendezVous = collect();
        if ($isCurrentUserMedecin) {
            $patientsAvecRendezVous = Patient::whereHas('rendezvous', function($query) use ($currentUser) {
                $query->where('medecin_id', $currentUser->id);
            })
            ->with(['rendezvous' => function($query) use ($currentUser) {
                $query->where('medecin_id', $currentUser->id)
                      ->where('date', '>=', now())
                      ->orderBy('date', 'asc')
                      ->limit(1);
            }])
            ->orderBy('nom')
            ->get()
            ->map(function($patient) {
                $patient->prochain_rdv = $patient->rendezvous->first()?->date;
                return $patient;
            });
        }

        $medecins = User::where('role', 'medecin')->get() ?? collect();

        // Date d'aujourd'hui
        $today = now()->format('Y-m-d');

        $selectedPatient = null;
        $consultations = collect();
        $examens = collect();
        $imageries = collect();
        $vaccinations = collect();
        $fichiers = collect();
        $habitudes = collect();
        $certificats = collect();
        $remarques = collect();
        $rendezvous = collect();

        if ($request->has('patient_id') && $request->patient_id) {
            $selectedPatient = Patient::find($request->patient_id);

            if ($selectedPatient) {
                try {
                    $consultations = Consultation::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('date_consultation', 'desc')->get();
                } catch (\Exception $e) {
                    $consultations = collect();
                }

                try {
                    $examens = ExamenBiologique::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('date_examen', 'desc')->get();
                } catch (\Exception $e) {
                    $examens = collect();
                }

                try {
                    $imageries = ImagerieMedicale::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('date_examen', 'desc')->get();
                } catch (\Exception $e) {
                    $imageries = collect();
                }

                try {
                    $vaccinations = Vaccination::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('date_vaccination', 'desc')->get();
                } catch (\Exception $e) {
                    $vaccinations = collect();
                }

                try {
                    $fichiers = FichierMedical::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('created_at', 'desc')->get();
                } catch (\Exception $e) {
                    $fichiers = collect();
                }

                try {
                    $habitudes = HabitudeVie::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('created_at', 'desc')->get();
                } catch (\Exception $e) {
                    $habitudes = collect();
                }

                try {
                    $certificats = Certificat::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('date_certificat', 'desc')->get();
                } catch (\Exception $e) {
                    $certificats = collect();
                }

                try {
                    $remarques = Remarque::where('patient_id', $selectedPatient->id)
                        ->with('medecin')->orderBy('date_remarque', 'desc')->get();
                } catch (\Exception $e) {
                    $remarques = collect();
                }

                try {
                    $rendezvous = Rendezvous::where('patient_id', $selectedPatient->id)
                        ->orderBy('date', 'desc')->get();
                } catch (\Exception $e) {
                    $rendezvous = collect();
                }
            }
        }

        return view('secretaire.dossier-medical', compact(
            'patientsAvecRendezVous', 'medecins', 'selectedPatient', 'consultations', 'examens', 
            'imageries', 'vaccinations', 'fichiers', 'habitudes', 'certificats', 'remarques', 'rendezvous',
            'currentUser', 'isCurrentUserMedecin', 'today'
        ));
    }

    public function storeConsultation(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date_consultation' => 'required|date',
            'heure' => 'nullable|date_format:H:i',
            'rendezvous_id' => 'nullable|exists:rendezvous,id',
            'motif' => 'required|string|max:1000',
            'symptomes' => 'nullable|string|max:2000',
            'diagnostic' => 'nullable|string|max:2000',
            'traitement' => 'nullable|string|max:2000',
            'follow_up_instructions' => 'nullable|string|max:2000',
            'consultation_fee' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:En cours,Terminée,Annulée,Reportée',
            'duree_traitement' => 'nullable|string|max:255'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        // Convertir l'heure en datetime pour la base de données
        if ($validated['heure']) {
            $validated['heure'] = $validated['date_consultation'] . ' ' . $validated['heure'] . ':00';
        }

        try {
            // Créer la consultation
            $consultation = Consultation::create($validated);

            // Générer automatiquement une ordonnance si traitement est fourni
            if (!empty($validated['traitement'])) {
                Ordonnance::create([
                    'patient_id' => $validated['patient_id'],
                    'medecin_id' => $validated['medecin_id'],
                    'date_ordonance' => $validated['date_consultation'],
                    'medicaments' => $validated['traitement'],
                    'duree_traitement' => $validated['duree_traitement'],
                    'instructions' => $validated['follow_up_instructions']
                ]);
                
                return redirect()->back()->with('success', 'Consultation ajoutée avec succès. Une ordonnance a été générée automatiquement.');
            }

            return redirect()->back()->with('success', 'Consultation ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la consultation', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la consultation.');
        }
    }

    public function storeCertificat(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date_certificat' => 'required|date',
            'type' => 'required|string|max:255',
            'contenu' => 'required|string|max:5000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        try {
            Certificat::create($validated);
            return redirect()->back()->with('success', 'Certificat ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du certificat', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création du certificat.');
        }
    }

    public function storeRemarque(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date_remarque' => 'required|date',
            'remarque' => 'required|string|max:2000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        try {
            Remarque::create($validated);
            return redirect()->back()->with('success', 'Remarque ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la remarque', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la remarque.');
        }
    }

    public function storeExamenBiologique(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'resultat' => 'required|string|max:1000',
            'unite' => 'nullable|string|max:50',
            'valeurs_reference' => 'nullable|string|max:255',
            'date_examen' => 'required|date',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        try {
            ExamenBiologique::create($validated);
            return redirect()->back()->with('success', 'Examen biologique ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'examen biologique', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de l\'examen biologique.');
        }
    }

    public function storeImagerieMedicale(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'zone_examinee' => 'required|string|max:255',
            'resultat' => 'required|string|max:2000',
            'date_examen' => 'required|date',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        try {
            ImagerieMedicale::create($validated);
            return redirect()->back()->with('success', 'Imagerie médicale ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'imagerie médicale', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de l\'imagerie médicale.');
        }
    }

    public function storeVaccination(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'nom' => 'required|string|max:255',
            'date_vaccination' => 'required|date',
            'date_rappel' => 'nullable|date|after:date_vaccination',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        try {
            Vaccination::create($validated);
            return redirect()->back()->with('success', 'Vaccination ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la vaccination', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la vaccination.');
        }
    }

    public function storeHabitudeVie(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'frequence' => 'nullable|string|max:255',
            'quantite' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date|before_or_equal:today',
            'date_fin' => 'nullable|date|after_or_equal:date_debut|before_or_equal:today',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        try {
            HabitudeVie::create($validated);
            return redirect()->back()->with('success', 'Habitude de vie ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'habitude de vie', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de l\'habitude de vie.');
        }
    }

    public function storeFichierMedical(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'nom' => 'required|string|max:255',
            'fichier' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'type' => 'required|string|max:100',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $validated['medecin_id'] = $request->input('medecin_id');
            $request->validate([
                'medecin_id' => 'required|exists:users,id'
            ]);
        }

        try {
            $fichier = $request->file('fichier');
            // Créer le dossier s'il n'existe pas
            $patientFolder = 'fichiers_medicaux/patient_' . $validated['patient_id'];
            if (!Storage::disk('public')->exists($patientFolder)) {
                Storage::disk('public')->makeDirectory($patientFolder);
            }

            // Générer un nom unique pour le fichier
            $originalName = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $fichier->getClientOriginalExtension();
            $fileName = $originalName . '_' . time() . '.' . $extension;
            $chemin = $fichier->storeAs($patientFolder, $fileName, 'public');

            FichierMedical::create([
                'patient_id' => $validated['patient_id'],
                'medecin_id' => $validated['medecin_id'],
                'nom' => $validated['nom'],
                'chemin' => $chemin,
                'type' => $validated['type'],
                'taille' => $fichier->getSize(),
                'commentaire' => $validated['commentaire'] ?? null
            ]);

            return redirect()->back()->with('success', 'Fichier médical ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'upload du fichier médical', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de l\'upload du fichier médical.');
        }
    }

    // Méthodes de modification
    public function updateConsultation(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:consultations,id',
            'patient_id' => 'required|exists:patients,id',
            'date_consultation' => 'required|date|before_or_equal:today',
            'heure' => 'nullable|date_format:H:i',
            'rendezvous_id' => 'nullable|exists:rendezvous,id',
            'motif' => 'required|string|max:1000',
            'symptomes' => 'nullable|string|max:2000',
            'diagnostic' => 'nullable|string|max:2000',
            'traitement' => 'nullable|string|max:2000',
            'follow_up_instructions' => 'nullable|string|max:2000',
            'consultation_fee' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:En cours,Terminée,Annulée,Reportée',
            'duree_traitement' => 'nullable|string|max:255'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            // Pour les secrétaires, utiliser le premier médecin disponible ou garder l'ancien
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        // Convertir l'heure en datetime pour la base de données
        if ($validated['heure']) {
            $validated['heure'] = $validated['date_consultation'] . ' ' . $validated['heure'] . ':00';
        }

        try {
            $consultation = Consultation::findOrFail($validated['id']);
            $consultation->update($validated);

            // Mettre à jour ou créer l'ordonnance associée si traitement est fourni
            if (!empty($validated['traitement'])) {
                // Chercher une ordonnance existante pour cette consultation/patient/médecin/date
                $ordonnance = Ordonnance::where('patient_id', $validated['patient_id'])
                    ->where('medecin_id', $validated['medecin_id'])
                    ->where('date_ordonance', $validated['date_consultation'])
                    ->first();

                if ($ordonnance) {
                    // Mettre à jour l'ordonnance existante
                    $ordonnance->update([
                        'medicaments' => $validated['traitement'],
                        'duree_traitement' => $validated['duree_traitement'],
                        'instructions' => $validated['follow_up_instructions']
                    ]);
                } else {
                    // Créer une nouvelle ordonnance
                    Ordonnance::create([
                        'patient_id' => $validated['patient_id'],
                        'medecin_id' => $validated['medecin_id'],
                        'date_ordonance' => $validated['date_consultation'],
                        'medicaments' => $validated['traitement'],
                        'duree_traitement' => $validated['duree_traitement'],
                        'instructions' => $validated['follow_up_instructions']
                    ]);
                }
                
                return redirect()->back()->with('success', 'Consultation modifiée avec succès. L\'ordonnance associée a été mise à jour.');
            }

            return redirect()->back()->with('success', 'Consultation modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de la consultation', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification de la consultation.');
        }
    }

    public function updateCertificat(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:certificats,id',
            'patient_id' => 'required|exists:patients,id',
            'date_certificat' => 'required|date',
            'type' => 'required|string|max:255',
            'contenu' => 'required|string|max:5000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        try {
            $certificat = Certificat::findOrFail($validated['id']);
            $certificat->update($validated);
            return redirect()->back()->with('success', 'Certificat modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du certificat', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification du certificat.');
        }
    }

    public function updateRemarque(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:remarques,id',
            'patient_id' => 'required|exists:patients,id',
            'date_remarque' => 'required|date',
            'remarque' => 'required|string|max:2000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        try {
            $remarque = Remarque::findOrFail($validated['id']);
            $remarque->update($validated);
            return redirect()->back()->with('success', 'Remarque modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de la remarque', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification de la remarque.');
        }
    }

    public function updateExamenBiologique(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:examens_biologiques,id',
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'resultat' => 'required|string|max:1000',
            'unite' => 'nullable|string|max:50',
            'valeurs_reference' => 'nullable|string|max:255',
            'date_examen' => 'required|date|before_or_equal:today',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        try {
            $examen = ExamenBiologique::findOrFail($validated['id']);
            $examen->update($validated);
            return redirect()->back()->with('success', 'Examen biologique modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de l\'examen biologique', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification de l\'examen biologique.');
        }
    }

    public function updateImagerieMedicale(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:imageries_medicales,id',
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'zone_examinee' => 'required|string|max:255',
            'resultat' => 'required|string|max:2000',
            'date_examen' => 'required|date|before_or_equal:today',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        try {
            $imagerie = ImagerieMedicale::findOrFail($validated['id']);
            $imagerie->update($validated);
            return redirect()->back()->with('success', 'Imagerie médicale modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de l\'imagerie médicale', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification de l\'imagerie médicale.');
        }
    }

    public function updateVaccination(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:vaccinations,id',
            'patient_id' => 'required|exists:patients,id',
            'nom' => 'required|string|max:255',
            'date_vaccination' => 'required|date|before_or_equal:today',
            'date_rappel' => 'nullable|date|after:date_vaccination',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        try {
            $vaccination = Vaccination::findOrFail($validated['id']);
            $vaccination->update($validated);
            return redirect()->back()->with('success', 'Vaccination modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de la vaccination', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification de la vaccination.');
        }
    }

    public function updateHabitudeVie(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:habitudes_vie,id',
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'frequence' => 'nullable|string|max:255',
            'quantite' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date|before_or_equal:today',
            'date_fin' => 'nullable|date|after_or_equal:date_debut|before_or_equal:today',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Utiliser l'ID du médecin connecté ou celui fourni dans la requête
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        try {
            $habitude = HabitudeVie::findOrFail($validated['id']);
            $habitude->update($validated);
            return redirect()->back()->with('success', 'Habitude de vie modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de l\'habitude de vie', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification de l\'habitude de vie.');
        }
    }

    public function updateFichierMedical(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:fichiers_medicales,id',
            'patient_id' => 'required|exists:patients,id',
            'nom' => 'required|string|max:255',
            'fichier' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'type' => 'required|string|max:100',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'medecin') {
            $validated['medecin_id'] = $currentUser->id;
        } else {
            $medecins = User::where('role', 'medecin')->first();
            $validated['medecin_id'] = $medecins ? $medecins->id : $request->input('medecin_id');
        }

        try {
            $fichierMedical = FichierMedical::findOrFail($validated['id']);

            $dataToUpdate = [
                'patient_id' => $validated['patient_id'],
                'medecin_id' => $validated['medecin_id'],
                'nom' => $validated['nom'],
                'type' => $validated['type'],
                'commentaire' => $validated['commentaire'] ?? null
            ];

            if ($request->hasFile('fichier')) {
                // Supprimer l'ancien fichier si un nouveau est téléchargé
                if ($fichierMedical->chemin && Storage::disk('public')->exists($fichierMedical->chemin)) {
                    Storage::disk('public')->delete($fichierMedical->chemin);
                }

                $fichier = $request->file('fichier');
                $patientFolder = 'fichiers_medicaux/patient_' . $validated['patient_id'];
                if (!Storage::disk('public')->exists($patientFolder)) {
                    Storage::disk('public')->makeDirectory($patientFolder);
                }
                $originalName = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $fichier->getClientOriginalExtension();
                $fileName = $originalName . '_' . time() . '.' . $extension;
                $chemin = $fichier->storeAs($patientFolder, $fileName, 'public');
                
                $dataToUpdate['chemin'] = $chemin;
                $dataToUpdate['taille'] = $fichier->getSize();
            }

            $fichierMedical->update($dataToUpdate);

            return redirect()->back()->with('success', 'Fichier médical modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du fichier médical', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la modification du fichier médical.');
        }
    }

    // Méthodes de suppression
    public function destroyConsultation(Request $request)
    {
        $id = $request->input('id');
        try {
            Consultation::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Consultation supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function destroyCertificat(Request $request)
    {
        $id = $request->input('id');
        try {
            Certificat::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Certificat supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function destroyRemarque(Request $request)
    {
        $id = $request->input('id');
        try {
            Remarque::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Remarque supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function destroyExamenBiologique(Request $request)
    {
        $id = $request->input('id');
        try {
            ExamenBiologique::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Examen biologique supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function destroyImagerieMedicale(Request $request)
    {
        $id = $request->input('id');
        try {
            ImagerieMedicale::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Imagerie médicale supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function destroyVaccination(Request $request)
    {
        $id = $request->input('id');
        try {
            Vaccination::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Vaccination supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function destroyHabitudeVie(Request $request)
    {
        $id = $request->input('id');
        try {
            HabitudeVie::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Habitude de vie supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function destroyFichierMedical(Request $request)
    {
        $id = $request->input('id');
        try {
            $fichier = FichierMedical::findOrFail($id);
            if (file_exists(storage_path('app/public/' . $fichier->chemin))) {
                unlink(storage_path('app/public/' . $fichier->chemin));
            }
            $fichier->delete();
            return redirect()->back()->with('success', 'Fichier médical supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }
}