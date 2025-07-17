<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Facture;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Paiement::with(['facture.patient']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('facture.patient', function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%");
                })->orWhereHas('facture', function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%");
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

            $paiements = $query->orderBy('created_at', 'desc')->paginate(10);

            $factures = Facture::with('patient')
                ->where('statut', '!=', 'payée')
                ->whereNotIn('id', function($query) {
                    $query->select('facture_id')
                          ->from('paiements')
                          ->where('statut', 'paye');
                })
                ->get();

        } catch (\Exception $e) {

            $paiements = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 10, 1, ['path' => request()->url()]
            );
            $factures = collect([]);
        }

        if ($request->wantsJson()) {
            return response()->json($paiements);
        }

        return view('secretaire.paiements', compact('paiements', 'factures'));
    }

    public function create()
    {
        // Récupérer seulement les factures non payées
        $factures = Facture::with('patient')
            ->where('statut', '!=', 'payée')
            ->whereNotIn('id', function($query) {
                $query->select('facture_id')
                      ->from('paiements')
                      ->where('statut', 'paye');
            })
            ->get();

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
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création du paiement.'], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la création du paiement.');
        }
    }

    public function show(Paiement $paiement, Request $request)
    {
        $paiement->load(['facture.patient']);

        if ($request->wantsJson()) {
            return response()->json($paiement);
        }

        return view('paiements.show', compact('paiement'));
    }

    public function edit(Paiement $paiement)
    {

        $factures = Facture::with('patient')
            ->where(function($query) use ($paiement) {
                $query->where('statut', '!=', 'payée')
                      ->orWhere('id', $paiement->facture_id);
            })
            ->whereNotIn('id', function($query) use ($paiement) {
                $query->select('facture_id')
                      ->from('paiements')
                      ->where('statut', 'paye')
                      ->where('id', '!=', $paiement->id);
            })
            ->get();

        return view('paiements.edit', compact('paiement', 'factures'));
    }

    public function update(Request $request, Paiement $paiement)
    {

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

        try {
            $ancienneFacture = $paiement->facture;
            $nouvelleFacture = Facture::find($validated['facture_id']);


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
                    'paiement' => $paiement
                ]);
            }

            return redirect()->route('secretaire.paiements')->with('success', 'Paiement mis à jour avec succès.');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la mise à jour du paiement.'], 500);
            }
            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la mise à jour du paiement.');
        }
    }


    public function destroy(Paiement $paiement, Request $request)
    {
        try {
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
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression du paiement.'], 500);
            }

            return redirect()->route('secretaire.paiements')->with('error', 'Erreur lors de la suppression du paiement.');
        }
    }
}
