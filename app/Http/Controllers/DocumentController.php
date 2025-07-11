<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\Facture;
use App\Models\Ordonnance;
use App\Models\User;
use App\Models\Patient;
use App\Models\Remarque;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request){
    $ordonns = Ordonnance::with(['patient', 'medecin'])->paginate(5);
    $certifs = Certificat::with(['patient', 'medecin'])->paginate(5);
    $remarqs = Remarque::with(['patient', 'medecin'])->paginate(5);

    if ($request->wantsJson()) {
        return response()->json([
            'ordonnances' => $ordonns,
            'certificats' => $certifs,
            'remarques' => $remarqs,
        ]);
    }

    // Build the documents array for the Blade view
    $documents = [
        'ordonnances' => $ordonns->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'ordonnance', // Fixed typo here
                'patient_cin' => $item->patient->cin ?? null,
                'patient_nom' => $item->patient->nom ?? null,
                'medecin_nom' => $item->medecin->nom ?? null,
                'instructions' => $item->instructions ?? null,
                'medicaments' => $item->medicaments ?? null,
                'duree_traitement' => $item->duree_traitement ?? null,
                'date' => $item->date_ordonance ?? null, // Unified date field
            ];
        }),
        'certificats' => $certifs->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'certificat',
                'patient_cin' => $item->patient->cin ?? null,
                'patient_nom' => $item->patient->nom ?? null,
                'medecin_nom' => $item->medecin->nom ?? null,
                'certificat_type' => $item->type ?? null, // Renamed to avoid conflict
                'contenu' => $item->contenu ?? null,
                'date' => $item->date_certificat ?? null, // Unified date field
            ];
        }),
        'remarques' => $remarqs->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'remarque',
                'patient_cin' => $item->patient->cin ?? null,
                'patient_nom' => $item->patient->nom ?? null,
                'medecin_nom' => $item->medecin->nom ?? null,
                'remarque' => $item->remarque ?? null,
                'date' => $item->date_remarque ?? null, // Unified date field
            ];
        }),
    ];

    return view('secretaire.documents', compact('documents'));
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
            'statut' => 'required|string|in:en attente,payée',
            'date' => 'required|date',
            // 'utilisateur_id' => 'required|exists:users,id',
        ]);

        $facture = Facture::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Facture créée avec succès.',
                'facture' => $facture
            ], 201);
        }

        return redirect()->back()->with('success', 'Facture créée avec succès.');
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
            // 'utilisateur_id' => 'required|exists:users,id',
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
