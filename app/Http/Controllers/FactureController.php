<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $factures = Facture::with(['patient', 'medecin', 'secretaire', 'utilisateur'])->paginate(10);
        } catch (\Exception $e) {
            // Si la table n'existe pas, créer une collection vide paginée
            $factures = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), // Collection vide
                0, // Total d'éléments
                10, // Éléments par page
                1, // Page actuelle
                ['path' => request()->url()] // Options
            );
        }

        $patients = Patient::all();
        $medecins = User::where('role', 'medecin')->get();
        $secretaires = User::where('role', 'secretaire')->get();
        $users = User::all(); // Variable que la vue attend

        if ($request->wantsJson()) {
            return response()->json($factures);
        }

        return view('secretaire.factures', compact('factures', 'patients', 'medecins', 'secretaires', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('factures.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'secretaire_id' => 'nullable|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|string',
            'date' => 'required|date',
            'utilisateur_id' => 'required|exists:users,id',
        ]);
        if (empty($validated['date'])) {
            $validated['date'] = now()->format('Y-m-d');
        }
        try {
            $facture = Facture::create($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Facture créée avec succès.',
                    'facture' => $facture
                ], 201);
            }

            return redirect()->route('secretaire.factures')->with('success', 'Facture créée avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création de la facture.'], 500);
            }

            return redirect()->route('secretaire.factures')->with('error', 'Erreur lors de la création de la facture.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Facture $facture)
    {
        if ($request->wantsJson()) {
            return response()->json($facture);
        }

        return view('factures.show', compact('facture'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Facture $facture)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('factures.edit', compact('facture'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facture $facture)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'secretaire_id' => 'nullable|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|string',
            'date' => 'required|date',
        ]);

        try {
            $facture->update($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Facture mise à jour avec succès.',
                    'facture' => $facture
                ]);
            }

            return redirect()->route('secretaire.factures')->with('success', 'Facture mise à jour avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la mise à jour de la facture.'], 500);
            }

            return redirect()->route('secretaire.factures')->with('error', 'Erreur lors de la mise à jour de la facture.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Facture $facture)
    {
        try {
            $facture->delete();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Facture supprimée avec succès.']);
            }

            return redirect()->route('secretaire.factures')->with('success', 'Facture supprimée avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression de la facture.'], 500);
            }

            return redirect()->route('secretaire.factures')->with('error', 'Erreur lors de la suppression de la facture.');
        }
    }
}
