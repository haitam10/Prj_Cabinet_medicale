<?php

namespace App\Http\Controllers;

use App\Models\Remarque;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemarqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer les remarques avec les relations
        $remarques = Remarque::with(['patient', 'medecin'])->get();
        
        // Transformer les données pour la vue
        $documents = $remarques->map(function ($remarque) {
            return [
                'id' => $remarque->id,
                'patient_id' => $remarque->patient_id,
                'medecin_id' => $remarque->medecin_id,
                'patient_cin' => $remarque->patient->cin ?? '',
                'patient_nom' => $remarque->patient->nom ?? '',
                'medecin_nom' => $remarque->medecin->nom ?? '',
                'remarque' => $remarque->remarque,
                'date' => $remarque->date_remarque,
            ];
        });

        // Récupérer tous les patients pour les dropdowns
        $patients = Patient::all();

        return view('secretaire.remarques', compact('documents', 'patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::all();
        return view('secretaire.remarques.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date_remarque' => 'required|date',
            'remarque' => 'required|string|max:2000',
        ]);

        // Ajouter automatiquement le médecin connecté
        $validated['medecin_id'] = Auth::id();
        
        try {
            $remarque = Remarque::create($validated);
            
            return redirect()->route('secretaire.remarques')
                    ->with('success', 'Remarque générée avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('secretaire.remarques')
                    ->with('error', 'Erreur lors de la création de la remarque: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $remarque = Remarque::with(['patient', 'medecin'])->find($id);
        
        if (!$remarque) {
            return response()->json(['message' => 'Remarque non trouvée'], 404);
        }
        
        return response()->json([
            'id' => $remarque->id,
            'patient_id' => $remarque->patient_id,
            'medecin_id' => $remarque->medecin_id,
            'patient_cin' => $remarque->patient->cin ?? '',
            'patient_nom' => $remarque->patient->nom ?? '',
            'medecin_nom' => $remarque->medecin->nom ?? '',
            'remarque' => $remarque->remarque,
            'date' => $remarque->date_remarque,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $remarque = Remarque::with(['patient', 'medecin'])->find($id);
        
        if (!$remarque) {
            return redirect()->route('secretaire.remarques')
                    ->with('error', 'Remarque non trouvée');
        }
        
        $patients = Patient::all();
        return view('secretaire.remarques.edit', compact('remarque', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $remarque = Remarque::find($id);
        
        if (!$remarque) {
            return redirect()->route('secretaire.remarques')
                    ->with('error', 'Remarque non trouvée');
        }
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date_remarque' => 'required|date',
            'remarque' => 'required|string|max:2000',
        ]);

        // Le médecin ne peut pas être modifié, on garde l'original
        // Pas besoin de valider medecin_id car il ne doit pas changer
        
        try {
            $remarque->update($validated);
            
            return redirect()->route('secretaire.remarques')
                    ->with('success', 'Remarque mise à jour avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('secretaire.remarques')
                    ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $remarque = Remarque::find($id);
        
        if (!$remarque) {
            return redirect()->route('secretaire.remarques')
                    ->with('error', 'Remarque non trouvée');
        }
        
        try {
            $remarque->delete();
            
            return redirect()->route('secretaire.remarques')
                        ->with('success', 'Remarque supprimée avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('secretaire.remarques')
                    ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Get all remarks as JSON (for API usage)
     */
    public function apiIndex()
    {
        $remarques = Remarque::with(['patient', 'medecin'])->get();
        
        return response()->json($remarques->map(function ($remarque) {
            return [
                'id' => $remarque->id,
                'patient_id' => $remarque->patient_id,
                'medecin_id' => $remarque->medecin_id,
                'patient_cin' => $remarque->patient->cin ?? '',
                'patient_nom' => $remarque->patient->nom ?? '',
                'medecin_nom' => $remarque->medecin->nom ?? '',
                'remarque' => $remarque->remarque,
                'date' => $remarque->date_remarque,
                'created_at' => $remarque->created_at,
                'updated_at' => $remarque->updated_at,
            ];
        }));
    }
}