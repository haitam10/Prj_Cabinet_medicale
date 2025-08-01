<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Facture;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Cabinet; 

class PaiementController extends Controller
{
public function index(Request $request)
{
    try {
        $user = Auth::user();

        // --- 1. Paiements avec relations
        $query = Paiement::with(['facture.patient']);

        // --- 2. Filtrage par rôle
        if ($user->role === 'medecin') {
            $query->whereHas('facture', function($q) use ($user) {
                $q->where('medecin_id', $user->id);
            });
        } elseif ($user->role === 'secretaire') {
            if ($user->medecin_id) {
                $query->whereHas('facture', function($q) use ($user) {
                    $q->where('medecin_id', $user->medecin_id);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // --- 3. Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('facture.patient', function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('cin', 'like', "%{$search}%");
                })->orWhereHas('facture', function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_from')) {
            $query->where('date_paiement', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_paiement', '<=', $request->date_to);
        }

        // --- 4. Paginate or get all
        $paiements = $query->orderBy('created_at', 'desc')->paginate(10);

        // --- 5. Groupement par patient
        $patientsGroupes = [];

        foreach ($paiements as $paiement) {
            if (!$paiement->facture || !$paiement->facture->patient) {
                continue;
            }

            $patient = $paiement->facture->patient;
            $patientId = $patient->id;

            if (!isset($patientsGroupes[$patientId])) {
                $patientsGroupes[$patientId] = [
                    'patient' => $patient,
                    'paiements' => [],
                    'nombre_paiements' => 0,
                    'total_paye' => 0,
                    'dernier_paiement' => null,
                    'statut_global' => 'en_attente',
                    'has_new_payments' => false,
                ];
            }

            $patientsGroupes[$patientId]['paiements'][] = $paiement;
            $patientsGroupes[$patientId]['nombre_paiements']++;

            if ($paiement->statut === 'paye') {
                $patientsGroupes[$patientId]['total_paye'] += $paiement->montant;
            }

            if (!$patientsGroupes[$patientId]['dernier_paiement'] || 
                $paiement->date_paiement > $patientsGroupes[$patientId]['dernier_paiement']->date_paiement) {
                $patientsGroupes[$patientId]['dernier_paiement'] = $paiement;
            }

            if (Carbon::parse($paiement->created_at)->diffInHours(Carbon::now()) < 24) {
                $patientsGroupes[$patientId]['has_new_payments'] = true;
            }
        }

        foreach ($patientsGroupes as &$patientData) {
            $statutsUniques = array_unique(array_column($patientData['paiements'], 'statut'));

            if (count($statutsUniques) === 1 && $statutsUniques[0] === 'paye') {
                $patientData['statut_global'] = 'paye';
            } elseif (in_array('paye', $statutsUniques)) {
                $patientData['statut_global'] = 'mixte';
            }
        }

        $patientsGroupes = array_values($patientsGroupes);

        // --- 6. Factures non payées
        $facturesQuery = Facture::with('patient')
            ->where('statut', '!=', 'payée')
            ->whereNotIn('id', function($query) {
                $query->select('facture_id')->from('paiements')->where('statut', 'paye');
            });

        if ($user->role === 'medecin') {
            $facturesQuery->where('medecin_id', $user->id);
        } elseif ($user->role === 'secretaire' && $user->medecin_id) {
            $facturesQuery->where('medecin_id', $user->medecin_id);
        }

        $factures = $facturesQuery->get();

        // --- 7. Charges du cabinet
        $charges = [];
        if ($user->role === 'medecin') {
            $cabinet = Cabinet::where('id_docteur', $user->id)->select('charges')->first();
            if ($cabinet && !is_null($cabinet->charges)) {
                $decodedCharges = json_decode($cabinet->charges, true);
                $charges = is_array($decodedCharges) ? $decodedCharges : [];
            }
        }

        // --- 8. Récupération des utilisateurs
        $patients = Patient::all();

        if ($user->role === 'medecin') {
            $medecins = User::where('role', 'medecin')->where('statut', 'actif')->where('id', $user->id)->get();
            $secretaires = User::where('role', 'secretaire')->where('statut', 'actif')->where('medecin_id', $user->id)->get();
        } elseif ($user->role === 'secretaire') {
            $medecins = User::where('role', 'medecin')->where('statut', 'actif')->where('id', $user->medecin_id)->get();
            $secretaires = User::where('role', 'secretaire')->where('statut', 'actif')->where('id', $user->id)->get();
        } else {
            $medecins = User::where('role', 'medecin')->where('statut', 'actif')->get();
            $secretaires = User::where('role', 'secretaire')->where('statut', 'actif')->get();
        }

    } catch (\Exception $e) {
        \Log::error('Erreur dans PaiementController@index: ' . $e->getMessage());

        $paiements = new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 10, 1, ['path' => request()->url()]);
        $patientsGroupes = [];
        $factures = collect([]);
        $patients = collect([]);
        $medecins = collect([]);
        $secretaires = collect([]);
        $charges = [];
    }

    if ($request->wantsJson()) {
        return response()->json([
            'paiements' => $paiements,
            'patientsGroupes' => $patientsGroupes,
            'factures' => $factures,
            'charges' => $charges,
            'medecins' => $medecins,
            'secretaires' => $secretaires,
        ]);
    }

    return view('secretaire.paiements', compact('paiements', 'patientsGroupes', 'factures', 'patients', 'medecins', 'secretaires', 'charges'));
}


    public function create()
    {
        // Récupérer seulement les factures non payées avec filtrage par rôle
        $facturesQuery = Facture::with('patient')
            ->where('statut', '!=', 'payée')
            ->whereNotIn('id', function($query) {
                $query->select('facture_id')
                      ->from('paiements')
                      ->where('statut', 'paye');
            });

        $user = Auth::user();
        if ($user->role === 'medecin') {
            $facturesQuery->where('medecin_id', $user->id);
        } elseif ($user->role === 'secretaire' && $user->medecin_id) {
            $facturesQuery->where('medecin_id', $user->medecin_id);
        }

        $factures = $facturesQuery->get();

        return view('paiements.create', compact('factures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facture_id' => 'required|exists:factures,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'statut' => 'required|string',
        ]);

        try {
            $facture = Facture::find($validated['facture_id']);
            if (!$facture) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Facture introuvable.'], 404);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Facture introuvable.');
            }

            // Vérifier les autorisations
            $user = Auth::user();
            if ($user->role === 'medecin' && $facture->medecin_id !== $user->id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à créer un paiement pour cette facture.');
            } elseif ($user->role === 'secretaire' && $facture->medecin_id !== $user->medecin_id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à créer un paiement pour cette facture.');
            }

            if ($facture->statut === 'payée') {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Cette facture est déjà payée.'], 400);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Cette facture est déjà payée.');
            }

            $paiementExistant = Paiement::where('facture_id', $validated['facture_id'])
                ->where('statut', 'paye')
                ->first();

            if ($paiementExistant) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Un paiement existe déjà pour cette facture.'], 400);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Un paiement existe déjà pour cette facture.');
            }

            $paiement = Paiement::create($validated);

            if ($validated['statut'] === 'paye') {
                $facture->update(['statut' => 'payée']);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Paiement créé avec succès.',
                    'paiement' => $paiement
                ], 201);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Paiement ajouté avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur dans PaiementController@store: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création du paiement: ' . $e->getMessage()], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la création du paiement.');
        }
    }

    public function storeManualAssignment(Request $request)
    {
        $validated = $request->validate([
            // Données de la facture
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'secretaire_id' => 'nullable|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'facture_statut' => 'required|string',
            'facture_date' => 'required|date',
            // Données du paiement
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'paiement_statut' => 'required|string',
        ]);

        try {
            // Vérifier les autorisations
            $user = Auth::user();
            if ($user->role === 'medecin' && $validated['medecin_id'] != $user->id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous ne pouvez créer des factures que pour vous-même.');
            } elseif ($user->role === 'secretaire' && $validated['medecin_id'] != $user->medecin_id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous ne pouvez créer des factures que pour votre médecin associé.');
            }

            DB::beginTransaction();

            // Créer la facture
            $facture = Facture::create([
                'patient_id' => $validated['patient_id'],
                'medecin_id' => $validated['medecin_id'],
                'secretaire_id' => $validated['secretaire_id'],
                'montant' => $validated['montant'],
                'statut' => $validated['facture_statut'],
                'date' => $validated['facture_date'],
                'utilisateur_id' => Auth::id(),
            ]);

            // Créer le paiement
            $paiement = Paiement::create([
                'facture_id' => $facture->id,
                'montant' => $validated['montant'],
                'date_paiement' => $validated['date_paiement'],
                'mode_paiement' => $validated['mode_paiement'],
                'statut' => $validated['paiement_statut'],
            ]);

            // Si le paiement est payé, mettre à jour le statut de la facture
            if ($validated['paiement_statut'] === 'paye') {
                $facture->update(['statut' => 'payée']);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Facture et paiement créés avec succès.',
                    'facture' => $facture,
                    'paiement' => $paiement
                ], 201);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Facture et paiement créés avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur dans PaiementController@storeManualAssignment: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création de la facture et du paiement: ' . $e->getMessage()], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la création de la facture et du paiement.');
        }
    }

    public function show($id, Request $request)
    {
        try {
            $paiement = Paiement::with(['facture.patient'])->findOrFail($id);

            // Vérifier les autorisations
            $user = Auth::user();
            if ($user->role === 'medecin' && $paiement->facture->medecin_id !== $user->id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à voir ce paiement.');
            } elseif ($user->role === 'secretaire' && $paiement->facture->medecin_id !== $user->medecin_id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à voir ce paiement.');
            }

            if ($request->wantsJson()) {
                return response()->json($paiement);
            }

            return view('paiements.show', compact('paiement'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans PaiementController@show: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Paiement introuvable.'], 404);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Paiement introuvable.');
        }
    }

    public function edit(Paiement $paiement)
    {
        // Vérifier les autorisations
        $user = Auth::user();
        if ($user->role === 'medecin' && $paiement->facture->medecin_id !== $user->id) {
            return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à modifier ce paiement.');
        } elseif ($user->role === 'secretaire' && $paiement->facture->medecin_id !== $user->medecin_id) {
            return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à modifier ce paiement.');
        }

        $facturesQuery = Facture::with('patient')
            ->where(function($query) use ($paiement) {
                $query->where('statut', '!=', 'payée')
                      ->orWhere('id', $paiement->facture_id);
            })
            ->whereNotIn('id', function($query) use ($paiement) {
                $query->select('facture_id')
                      ->from('paiements')
                      ->where('statut', 'paye')
                      ->where('id', '!=', $paiement->id);
            });

        // Appliquer le filtrage par rôle
        if ($user->role === 'medecin') {
            $facturesQuery->where('medecin_id', $user->id);
        } elseif ($user->role === 'secretaire' && $user->medecin_id) {
            $facturesQuery->where('medecin_id', $user->medecin_id);
        }

        $factures = $facturesQuery->get();

        return view('paiements.edit', compact('paiement', 'factures'));
    }

    public function update(Request $request, $id)
    {
        try {
            $paiement = Paiement::findOrFail($id);

            // Vérifier les autorisations
            $user = Auth::user();
            if ($user->role === 'medecin' && $paiement->facture->medecin_id !== $user->id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à modifier ce paiement.');
            } elseif ($user->role === 'secretaire' && $paiement->facture->medecin_id !== $user->medecin_id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à modifier ce paiement.');
            }

            if ($paiement->statut === 'paye') {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Impossible de modifier un paiement payé.'], 403);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Impossible de modifier un paiement payé.');
            }

            $validated = $request->validate([
                'facture_id' => 'required|exists:factures,id',
                'montant' => 'required|numeric|min:0',
                'date_paiement' => 'required|date',
                'mode_paiement' => 'required|string',
                'statut' => 'required|string',
            ]);

            $ancienneFacture = $paiement->facture;
            $nouvelleFacture = Facture::find($validated['facture_id']);

            // Vérifier les autorisations pour la nouvelle facture
            if ($user->role === 'medecin' && $nouvelleFacture->medecin_id !== $user->id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous ne pouvez affecter le paiement qu\'à vos propres factures.');
            } elseif ($user->role === 'secretaire' && $nouvelleFacture->medecin_id !== $user->medecin_id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous ne pouvez affecter le paiement qu\'aux factures de votre médecin associé.');
            }

            if ($paiement->facture_id != $validated['facture_id']) {
                if ($nouvelleFacture->statut === 'payée') {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => 'Cette facture est déjà payée.'], 400);
                    }
                    return redirect()->route('secretaire.paiements')->with('error', 'Cette facture est déjà payée.');
                }

                $paiementExistant = Paiement::where('facture_id', $validated['facture_id'])
                    ->where('statut', 'paye')
                    ->where('id', '!=', $paiement->id)
                    ->first();

                if ($paiementExistant) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => 'Un paiement existe déjà pour cette facture.'], 400);
                    }
                    return redirect()->route('secretaire.paiements')->with('error', 'Un paiement existe déjà pour cette facture.');
                }
            }

            $ancienStatut = $paiement->statut;
            $paiement->update($validated);

            // Gestion des statuts de facture
            if ($ancienStatut !== $validated['statut']) {
                if ($validated['statut'] === 'paye') {
                    $nouvelleFacture->update(['statut' => 'payée']);
                } elseif ($ancienStatut === 'paye') {
                    $nouvelleFacture->update(['statut' => 'en_attente']);
                }
            }

            // Si on a changé de facture
            if ($ancienneFacture && $ancienneFacture->id !== $nouvelleFacture->id) {
                if ($ancienStatut === 'paye') {
                    $ancienneFacture->update(['statut' => 'en_attente']);
                }
                if ($validated['statut'] === 'paye') {
                    $nouvelleFacture->update(['statut' => 'payée']);
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Paiement mis à jour avec succès.',
                    'paiement' => $paiement->fresh()
                ]);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Paiement mis à jour avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur dans PaiementController@update: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la mise à jour du paiement: ' . $e->getMessage()], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la mise à jour du paiement.');
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $paiement = Paiement::findOrFail($id);

            // Vérifier les autorisations
            $user = Auth::user();
            if ($user->role === 'medecin' && $paiement->facture->medecin_id !== $user->id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à supprimer ce paiement.');
            } elseif ($user->role === 'secretaire' && $paiement->facture->medecin_id !== $user->medecin_id) {
                return redirect()->route('secretaire.paiements')->with('error', 'Vous n\'êtes pas autorisé à supprimer ce paiement.');
            }

            $facture = $paiement->facture;
            $statutPaiement = $paiement->statut;

            $paiement->delete();

            if ($statutPaiement === 'paye' && $facture) {
                $facture->update(['statut' => 'en_attente']);
            }

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Paiement supprimé avec succès.']);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Paiement supprimé avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur dans PaiementController@destroy: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression du paiement: ' . $e->getMessage()], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la suppression du paiement.');
        }
    }

    /**
     * Récupérer l'historique des paiements d'un patient
     */
    public function getPatientPaymentHistory($patientId, Request $request)
    {
        try {
            $patient = Patient::findOrFail($patientId);
            
            $paymentsQuery = Paiement::whereHas('facture', function($query) use ($patientId) {
                $query->where('patient_id', $patientId);
            })->with(['facture']);

            // Appliquer le filtrage par rôle
            $user = Auth::user();
            if ($user->role === 'medecin') {
                $paymentsQuery->whereHas('facture', function($query) use ($user) {
                    $query->where('medecin_id', $user->id);
                });
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $paymentsQuery->whereHas('facture', function($query) use ($user) {
                    $query->where('medecin_id', $user->medecin_id);
                });
            }

            $payments = $paymentsQuery->orderBy('date_paiement', 'desc')->get();

            if ($request->wantsJson()) {
                return response()->json([
                    'patient' => $patient,
                    'payments' => $payments
                ]);
            }

            return view('patients.payment-history', compact('patient', 'payments'));

        } catch (\Exception $e) {
            \Log::error('Erreur dans PaiementController@getPatientPaymentHistory: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors du chargement de l\'historique: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors du chargement de l\'historique.');
        }
    }
       public function storeCharges(Request $request)
    {
        try {
            // Validate the incoming request data for charges, including the new 'type' field
            $request->validate([
                'charges' => 'required|array',
                'charges.*.type' => 'required|string|in:"Cabinet charges",Salaires,Crédits,Autre', // Added validation for type
                'charges.*.key' => 'required|string|max:255',
                'charges.*.value' => 'required|numeric|min:0',
            ]);

            $idDocteur = Auth::id();

            if (!$idDocteur) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'User not authenticated.'], 401);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'User not authenticated.');
            }

            // Find the cabinet associated with the authenticated doctor
            $cabinet = Cabinet::where('id_docteur', $idDocteur)->first();

            if (!$cabinet) {
                // If no cabinet found for the doctor, return an error response
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Cabinet not found for this doctor.'], 404);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Cabinet not found for this doctor.');
            }

            // Get existing charges from the cabinet, or initialize an empty array if none exist
            $existingCharges = $cabinet->charges ? json_decode($cabinet->charges, true) : [];

            // Prepare new charges to be added, including 'created_at' and a unique 'id'
            $newChargesData = [];
            foreach ($request->input('charges') as $newCharge) {
                $newChargesData[] = [
                    'id' => uniqid(), // Generate a unique ID for each new charge
                    'id_docteur' => $idDocteur,
                    'type' => $newCharge['type'], // Store the new type
                    'key' => $newCharge['key'],
                    'value' => (float) $newCharge['value'], // Ensure value is a float
                    'created_at' => now()->format('Y-m-d H:i:s'), // Current timestamp
                ];
            }

            // Merge new charges with existing ones
            $updatedCharges = array_merge($existingCharges, $newChargesData);

            // Store the updated charges array back into the 'charges' JSON column
            $cabinet->charges = json_encode($updatedCharges);

            // Save the cabinet model
            $cabinet->save();

            // Return success response based on request type
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Charges added successfully!',
                    'charges' => $newChargesData // You might want to return the full updated list or just the new ones
                ], 201);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Charges added successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Handle any exceptions during the process
            Log::error("Error storing charges: " . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création des charges: ' . $e->getMessage()], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la création des charges.');
        }
    }

    public function editCharges(Request $request, $chargeId)
    {
        try {
            // Validate the incoming request data for the updated charge, including 'type'
            $request->validate([
                'type' => 'required|string|in:"Cabinet charges",Salaires,Crédits,Autre', // Added validation for type
                'key' => 'required|string|max:255',
                'value' => 'required|numeric|min:0',
            ]);

            $idDocteur = Auth::id();

            if (!$idDocteur) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'User not authenticated.'], 401);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'User not authenticated.');
            }

            $cabinet = Cabinet::where('id_docteur', $idDocteur)->first();

            if (!$cabinet) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Cabinet not found for this doctor.'], 404);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Cabinet not found for this doctor.');
            }

            $existingCharges = $cabinet->charges ? json_decode($cabinet->charges, true) : [];
            $chargeFound = false;

            foreach ($existingCharges as $index => $charge) {
                if ($charge['id'] === $chargeId) {
                    $existingCharges[$index]['type'] = $request->input('type'); // Update the type
                    $existingCharges[$index]['key'] = $request->input('key');
                    $existingCharges[$index]['value'] = (float) $request->input('value');
                    $chargeFound = true;
                    break;
                }
            }

            if (!$chargeFound) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Charge not found.'], 404);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Charge not found.');
            }

            $cabinet->charges = json_encode($existingCharges);
            $cabinet->save();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Charge updated successfully!'], 200);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Charge updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error updating charge: " . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la mise à jour de la charge: ' . $e->getMessage()], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la mise à jour de la charge.');
        }
    }

    public function deleteCharges(Request $request, $chargeId)
    {
        try {
            $idDocteur = Auth::id();

            if (!$idDocteur) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'User not authenticated.'], 401);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'User not authenticated.'); // Replace 'your.charges.route'
            }

            // Find the cabinet associated with the authenticated doctor
            $cabinet = Cabinet::where('id_docteur', $idDocteur)->first();

            if (!$cabinet) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Cabinet not found for this doctor.'], 404);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Cabinet not found for this doctor.'); // Replace 'your.charges.route'
            }

            $existingCharges = $cabinet->charges ? json_decode($cabinet->charges, true) : [];

            $updatedCharges = array_filter($existingCharges, function ($charge) use ($chargeId) {
                return $charge['id'] !== $chargeId;
            });

            if (count($existingCharges) === count($updatedCharges)) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Charge not found or already deleted.'], 404);
                }
                return redirect()->route('secretaire.paiements')->with('error', 'Charge not found or already deleted.'); // Replace 'your.charges.route'
            }

            // Re-index the array to ensure it's a clean JSON array (optional but good practice)
            $updatedCharges = array_values($updatedCharges);

            // Store the updated charges array back into the 'charges' JSON column
            $cabinet->charges = json_encode($updatedCharges);

            // Save the cabinet model
            $cabinet->save();

            // Return success response based on request type
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Charge deleted successfully!'], 200);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Charge deleted successfully!'); // Replace 'your.charges.route'

        } catch (\Exception $e) {
            // Handle any exceptions during the process
            Log::error("Error deleting charge: " . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression de la charge: ' . $e->getMessage()], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la suppression de la charge.'); // Replace 'your.charges.route'
        }
    }

    public function showCharge(Request $request, $chargeId)
    {
        try {
            // Get the authenticated user's ID (id_docteur)
            $idDocteur = Auth::id();

            if (!$idDocteur) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            // Find the cabinet associated with the authenticated doctor
            $cabinet = Cabinet::where('id_docteur', $idDocteur)->first();

            if (!$cabinet || is_null($cabinet->charges)) {
                return response()->json(['error' => 'Charges not found for this doctor or cabinet has no charges.'], 404);
            }

            // Decode the JSON string from the 'charges' column into a PHP array
            $charges = json_decode($cabinet->charges, true);
            $foundCharge = null;

            // Iterate through charges to find the specific one by its unique 'id'
            foreach ($charges as $charge) {
                if ($charge['id'] === $chargeId) {
                    $foundCharge = $charge;
                    break; // Stop once the charge is found
                }
            }

            if (!$foundCharge) {
                return response()->json(['error' => 'Charge not found.'], 404);
            }

            // Return the found charge as a JSON response
            return response()->json($foundCharge, 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Error fetching single charge: " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement de la charge: ' . $e->getMessage()], 500);
        }
    }
}