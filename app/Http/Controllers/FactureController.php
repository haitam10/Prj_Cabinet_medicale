<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $factures = Facture::paginate(10);

        if ($request->wantsJson()) {
            return response()->json($factures);
        }

        return view('factures.index', compact('factures'));
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

        $facture = Facture::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Facture créée avec succès.',
                'facture' => $facture
            ], 201);
        }

        return redirect()->route('factures.index')->with('success', 'Facture créée avec succès.');
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
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        $facture->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Facture mise à jour avec succès.',
                'facture' => $facture
            ]);
        }

        return redirect()->route('factures.index')->with('success', 'Facture mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Facture $facture)
    {
        $facture->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Facture supprimée avec succès.']);
        }

        return redirect()->route('factures.index')->with('success', 'Facture supprimée avec succès.');
    }
}
