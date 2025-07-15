<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Facture;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $paiements = Paiement::with('facture')->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($paiements);
        }

        return view('secretaire.paiements', compact('paiements'));
    }

    public function create()
    {
        $factures = Facture::all();
        return view('paiements.create', compact('factures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facture_id' => 'required|exists:factures,id',
            'montant' => 'required|numeric',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'statut' => 'required|string',
        ]);

        $paiement = Paiement::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Paiement créé avec succès.',
                'paiement' => $paiement
            ], 201);
        }

        return redirect()->route('paiements.index')->with('success', 'Paiement ajouté avec succès.');
    }

    public function show(Paiement $paiement, Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json($paiement);
        }

        return view('paiements.show', compact('paiement'));
    }

    public function edit(Paiement $paiement)
    {
        $factures = Facture::all();
        return view('paiements.edit', compact('paiement', 'factures'));
    }

    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'facture_id' => 'required|exists:factures,id',
            'montant' => 'required|numeric',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'statut' => 'required|string',
        ]);

        $paiement->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Paiement mis à jour avec succès.',
                'paiement' => $paiement
            ]);
        }

        return redirect()->route('paiements.index')->with('success', 'Paiement mis à jour avec succès.');
    }

    public function destroy(Paiement $paiement, Request $request)
    {
        $paiement->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Paiement supprimé avec succès.']);
        }

        return redirect()->route('paiements.index')->with('success', 'Paiement supprimé avec succès.');
    }
}
