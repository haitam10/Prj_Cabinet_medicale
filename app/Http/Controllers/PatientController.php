<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\HabitudeVie;
use App\Models\ImagerieMedicale;
use App\Models\Vaccination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les patients avec le montant total payé calculé via les relations Eloquent
        $patients = Patient::with(['factures.paiements' => function($query) {
            $query->where('statut', 'paye');
        }])->paginate(10);

        // Calculer le montant total payé pour chaque patient
        foreach ($patients as $patient) {
            $patient->montant_paye = $patient->factures->sum(function($facture) {
                return $facture->paiements->sum('montant');
            });
        }

        if ($request->wantsJson()) {
            return response()->json($patients);
        }

        return view('secretaire.patients', compact('patients'));
    }

    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('patients.create');
    }

    public function store(Request $request)
    {
        Log::info('Tentative de création de patient', $request->all());

        $rules = [
            'cin' => 'required|string|unique:patients,cin',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:homme,femme',
            'date_naissance' => 'required|date|before_or_equal:today',
            'contact' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'telephone_secondaire' => 'nullable|string|max:255',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'antecedents' => 'nullable|string|max:1000',
            'medicaments' => 'nullable|string|max:1000',
            'poids' => 'nullable|numeric|min:0|max:999.99',
            'taille' => 'nullable|numeric|min:0|max:999.99',
            'profession' => 'nullable|string|max:255',
            'situation_familiale' => 'nullable|in:celibataire,marie,divorce,veuf',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ];

        $messages = [
            'cin.unique' => 'Le patient existe déjà ! Un patient avec ce CIN est déjà enregistré dans le système.',
            'cin.required' => 'Le CIN est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être homme ou femme.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'contact.required' => 'Le téléphone principal est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'poids.numeric' => 'Le poids doit être un nombre.',
            'taille.numeric' => 'La taille doit être un nombre.',
            'emergency_contact_name.max' => 'Le nom du contact d\'urgence ne peut pas dépasser 100 caractères.',
            'emergency_contact_phone.max' => 'Le téléphone du contact d\'urgence ne peut pas dépasser 20 caractères.',
            'profile_image.image' => 'Le fichier doit être une image.',
            'profile_image.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'profile_image.max' => 'L\'image ne peut pas dépasser 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            $validated['password_hash'] = Hash::make('default123');
            $validated['is_active'] = $validated['is_active'] ?? true;

            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('patient_profiles', 'public');
                $validated['profile_image'] = $imagePath;

                // === Ajout pour copie automatique dans public ===
                $sourcePath = storage_path('app/public/' . $imagePath);
                $destinationPath = public_path('storage/' . $imagePath);
                File::ensureDirectoryExists(dirname($destinationPath));
                File::copy($sourcePath, $destinationPath);
            }

            $patient = Patient::create($validated);

            Log::info('Patient créé avec succès', ['patient_id' => $patient->id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Patient créé avec succès.',
                    'patient' => $patient
                ], 201);
            }

            return redirect()->route('secretaire.patients')->with('success', 'Patient ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du patient', ['error' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création du patient.'], 500);
            }

            return redirect()->route('secretaire.patients')->with('error', 'Erreur lors de la création du patient : ' . $e->getMessage());
        }
    }

    public function show(Request $request, Patient $patient)
    {
        if ($request->wantsJson()) {
            return response()->json($patient);
        }

        return view('patients.show', compact('patient'));
    }

    public function edit(Request $request, Patient $patient)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $rules = [
            'cin' => 'required|string|unique:patients,cin,' . $patient->id,
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:homme,femme',
            'date_naissance' => 'required|date|before_or_equal:today',
            'contact' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'telephone_secondaire' => 'nullable|string|max:255',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'antecedents' => 'nullable|string|max:1000',
            'medicaments' => 'nullable|string|max:1000',
            'poids' => 'nullable|numeric|min:0|max:999.99',
            'taille' => 'nullable|numeric|min:0|max:999.99',
            'profession' => 'nullable|string|max:255',
            'situation_familiale' => 'nullable|in:celibataire,marie,divorce,veuf',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ];

        $messages = [
            'cin.unique' => 'Le patient existe déjà ! Un patient avec ce CIN est déjà enregistré dans le système.',
            'cin.required' => 'Le CIN est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être homme ou femme.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'contact.required' => 'Le téléphone principal est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'poids.numeric' => 'Le poids doit être un nombre.',
            'taille.numeric' => 'La taille doit être un nombre.',
            'emergency_contact_name.max' => 'Le nom du contact d\'urgence ne peut pas dépasser 100 caractères.',
            'emergency_contact_phone.max' => 'Le téléphone du contact d\'urgence ne peut pas dépasser 20 caractères.',
            'profile_image.image' => 'Le fichier doit être une image.',
            'profile_image.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'profile_image.max' => 'L\'image ne peut pas dépasser 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            if ($request->hasFile('profile_image')) {
                if ($patient->profile_image && \Storage::disk('public')->exists($patient->profile_image)) {
                    \Storage::disk('public')->delete($patient->profile_image);
                }

                $imagePath = $request->file('profile_image')->store('patient_profiles', 'public');
                $validated['profile_image'] = $imagePath;

                // === Ajout pour copie automatique dans public ===
                $sourcePath = storage_path('app/public/' . $imagePath);
                $destinationPath = public_path('storage/' . $imagePath);
                File::ensureDirectoryExists(dirname($destinationPath));
                File::copy($sourcePath, $destinationPath);
            }

            $patient->update($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Patient mis à jour avec succès.',
                    'patient' => $patient
                ]);
            }

            return redirect()->route('secretaire.patients')->with('success', 'Patient mis à jour avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la mise à jour du patient.'], 500);
            }

            return redirect()->route('secretaire.patients')->with('error', 'Erreur lors de la mise à jour du patient : ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Patient $patient)
    {
        try {
            if ($patient->profile_image && \Storage::disk('public')->exists($patient->profile_image)) {
                \Storage::disk('public')->delete($patient->profile_image);
            }

            $patient->delete();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Patient supprimé avec succès.']);
            }

            return redirect()->route('secretaire.patients')->with('success', 'Patient supprimé avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression du patient.'], 500);
            }

            return redirect()->route('secretaire.patients')->with('error', 'Erreur lors de la suppression du patient.');
        }
    }

    // MÉTHODE CORRIGÉE POUR AFFICHER TOUTES LES DONNÉES DU PATIENT
    public function getPatientDetails(Request $request, Patient $patient)
    {
        try {
            // Charger le patient avec les consultations
            $patient->load([
                'consultations' => function($query) {
                    $query->with(['medecin', 'rendezvous'])->orderBy('date_consultation', 'desc');
                }
            ]);

            // Récupérer les consultations avec leurs rendez-vous associés
            $consultations = $patient->consultations;

            // Récupérer les ordonnances directement depuis la base de données
            $ordonnances = collect();
            try {
                $ordonnances = DB::table('ordonnances')
                    ->leftJoin('users', 'ordonnances.medecin_id', '=', 'users.id')
                    ->where('ordonnances.patient_id', $patient->id)
                    ->select(
                        'ordonnances.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('ordonnances.created_at', 'desc')
                    ->get()
                    ->map(function($ordonnance) {
                        return (object) [
                            'id' => $ordonnance->id,
                            'medicaments' => $ordonnance->medicaments,
                            'duree_traitement' => $ordonnance->duree_traitement,
                            'instructions' => $ordonnance->instructions,
                            'date_ordonance' => $ordonnance->date_ordonance,
                            'created_at' => $ordonnance->created_at,
                            'medecin' => $ordonnance->medecin_nom ? (object) ['nom' => $ordonnance->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération des ordonnances', ['error' => $e->getMessage()]);
            }

            // Récupérer les certificats directement depuis la base de données
            $certificats = collect();
            try {
                $certificats = DB::table('certificats')
                    ->leftJoin('users', 'certificats.medecin_id', '=', 'users.id')
                    ->where('certificats.patient_id', $patient->id)
                    ->select(
                        'certificats.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('certificats.created_at', 'desc')
                    ->get()
                    ->map(function($certificat) {
                        return (object) [
                            'id' => $certificat->id,
                            'type' => $certificat->type,
                            'contenu' => $certificat->contenu,
                            'date_certificat' => $certificat->date_certificat,
                            'created_at' => $certificat->created_at,
                            'medecin' => $certificat->medecin_nom ? (object) ['nom' => $certificat->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération des certificats', ['error' => $e->getMessage()]);
            }

            // Récupérer les remarques directement depuis la base de données
            $remarques = collect();
            try {
                $remarques = DB::table('remarques')
                    ->leftJoin('users', 'remarques.medecin_id', '=', 'users.id')
                    ->where('remarques.patient_id', $patient->id)
                    ->select(
                        'remarques.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('remarques.date_remarque', 'desc')
                    ->get()
                    ->map(function($remarque) {
                        return (object) [
                            'id' => $remarque->id,
                            'remarque' => $remarque->remarque,
                            'date_remarque' => $remarque->date_remarque,
                            'created_at' => $remarque->created_at,
                            'medecin' => $remarque->medecin_nom ? (object) ['nom' => $remarque->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération des remarques', ['error' => $e->getMessage()]);
            }

            // Récupérer les habitudes de vie directement depuis la base de données
            $habitudesVie = collect();
            try {
                $habitudesVie = DB::table('habitudes_vie')
                    ->leftJoin('users', 'habitudes_vie.medecin_id', '=', 'users.id')
                    ->where('habitudes_vie.patient_id', $patient->id)
                    ->select(
                        'habitudes_vie.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('habitudes_vie.created_at', 'desc')
                    ->get()
                    ->map(function($habitude) {
                        return (object) [
                            'id' => $habitude->id,
                            'type' => $habitude->type,
                            'description' => $habitude->description,
                            'frequence' => $habitude->frequence,
                            'quantite' => $habitude->quantite,
                            'date_debut' => $habitude->date_debut,
                            'date_fin' => $habitude->date_fin,
                            'commentaire' => $habitude->commentaire,
                            'created_at' => $habitude->created_at,
                            'medecin' => $habitude->medecin_nom ? (object) ['nom' => $habitude->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération des habitudes de vie', ['error' => $e->getMessage()]);
            }

            // Récupérer les examens biologiques directement depuis la base de données
            $examensBiologiques = collect();
            try {
                $examensBiologiques = DB::table('examens_biologiques')
                    ->leftJoin('users', 'examens_biologiques.medecin_id', '=', 'users.id')
                    ->where('examens_biologiques.patient_id', $patient->id)
                    ->select(
                        'examens_biologiques.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('examens_biologiques.date_examen', 'desc')
                    ->get()
                    ->map(function($examen) {
                        return (object) [
                            'id' => $examen->id,
                            'type' => $examen->type,
                            'resultat' => $examen->resultat,
                            'unite' => $examen->unite,
                            'valeurs_reference' => $examen->valeurs_reference,
                            'date_examen' => $examen->date_examen,
                            'commentaire' => $examen->commentaire,
                            'created_at' => $examen->created_at,
                            'medecin' => $examen->medecin_nom ? (object) ['nom' => $examen->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération des examens biologiques', ['error' => $e->getMessage()]);
            }

            // Récupérer l'imagerie médicale directement depuis la base de données
            $imagerieMedicale = collect();
            try {
                $imagerieMedicale = DB::table('imageries_medicales')
                    ->leftJoin('users', 'imageries_medicales.medecin_id', '=', 'users.id')
                    ->where('imageries_medicales.patient_id', $patient->id)
                    ->select(
                        'imageries_medicales.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('imageries_medicales.date_examen', 'desc')
                    ->get()
                    ->map(function($imagerie) {
                        return (object) [
                            'id' => $imagerie->id,
                            'type' => $imagerie->type,
                            'zone_examinee' => $imagerie->zone_examinee,
                            'resultat' => $imagerie->resultat,
                            'date_examen' => $imagerie->date_examen,
                            'commentaire' => $imagerie->commentaire,
                            'created_at' => $imagerie->created_at,
                            'medecin' => $imagerie->medecin_nom ? (object) ['nom' => $imagerie->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération de l\'imagerie médicale', ['error' => $e->getMessage()]);
            }

            // Récupérer les vaccinations directement depuis la base de données
            $vaccinations = collect();
            try {
                $vaccinations = DB::table('vaccinations')
                    ->leftJoin('users', 'vaccinations.medecin_id', '=', 'users.id')
                    ->where('vaccinations.patient_id', $patient->id)
                    ->select(
                        'vaccinations.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('vaccinations.date_vaccination', 'desc')
                    ->get()
                    ->map(function($vaccination) {
                        return (object) [
                            'id' => $vaccination->id,
                            'nom' => $vaccination->nom,
                            'date_vaccination' => $vaccination->date_vaccination,
                            'date_rappel' => $vaccination->date_rappel,
                            'commentaire' => $vaccination->commentaire,
                            'created_at' => $vaccination->created_at,
                            'medecin' => $vaccination->medecin_nom ? (object) ['nom' => $vaccination->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération des vaccinations', ['error' => $e->getMessage()]);
            }

            // Récupérer les fichiers médicaux directement depuis la base de données
            $fichiersMedicaux = collect();
            try {
                $fichiersMedicaux = DB::table('fichiers_medicaux')
                    ->leftJoin('users', 'fichiers_medicaux.medecin_id', '=', 'users.id')
                    ->where('fichiers_medicaux.patient_id', $patient->id)
                    ->select(
                        'fichiers_medicaux.*',
                        'users.nom as medecin_nom'
                    )
                    ->orderBy('fichiers_medicaux.created_at', 'desc')
                    ->get()
                    ->map(function($fichier) {
                        return (object) [
                            'id' => $fichier->id,
                            'nom' => $fichier->nom,
                            'type' => $fichier->type,
                            'taille' => $fichier->taille,
                            'commentaire' => $fichier->commentaire,
                            'created_at' => $fichier->created_at,
                            'medecin' => $fichier->medecin_nom ? (object) ['nom' => $fichier->medecin_nom] : null
                        ];
                    });
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la récupération des fichiers médicaux', ['error' => $e->getMessage()]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'patient' => $patient,
                    'consultations' => $consultations,
                    'ordonnances' => $ordonnances,
                    'certificats' => $certificats,
                    'remarques' => $remarques,
                    'habitudesVie' => $habitudesVie,
                    'examensBiologiques' => $examensBiologiques,
                    'imagerieMedicale' => $imagerieMedicale,
                    'vaccinations' => $vaccinations,
                    'fichiersMedicaux' => $fichiersMedicaux
                ]);
            }

            return response()->json(['error' => 'Requête non valide'], 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des détails du patient', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Erreur lors de la récupération des données: ' . $e->getMessage()], 500);
        }
    }
}