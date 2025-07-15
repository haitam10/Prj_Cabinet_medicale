<?php

namespace App\Http\Controllers;

use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\User;


use Illuminate\Http\Request;

class OrdonnanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'medecin_id' => 'required|exists:users,id',
        'medicaments' => 'required|string',
        'instructions' => 'required|string',
        'duree_traitement' => 'required|string',
        'date_ordonnance' => 'required|date',
    ]);

    try {
        $patient = Patient::findOrFail($request->patient_id);
        $medecin = User::findOrFail($request->medecin_id);

        $ordonnance = new Ordonnance();
        $ordonnance->patient_id = $request->patient_id;
        $ordonnance->medecin_id = $request->medecin_id;
        $ordonnance->medicaments = $request->medicaments;
        $ordonnance->instructions = $request->instructions;
        $ordonnance->duree_traitement = $request->duree_traitement;
        $ordonnance->date_ordonance = $request->date_ordonnance;
        $ordonnance->save();

        // Prepare for printing
        $documentData = [
            'patient_cin' => $patient->cin,
            'patient_nom' => $patient->nom,
            'medecin_nom' => $medecin->nom,
            'medicaments' => $request->medicaments,
            'instructions' => $request->instructions,
            'duree_traitement' => $request->duree_traitement,
            'date' => $request->date_ordonnance,
        ];

        session(['print_ordonnance' => $documentData]);

        return redirect()->route('secretaire.ordonnances')
            ->with('success', 'Ordonnance générée avec succès!')
            ->with('print_document', true);

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Erreur lors de la génération de l\'ordonnance: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Ordonnance $ordonnance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ordonnance $ordonnance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ordonnance $ordonnance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ordonnance $ordonnance)
    {
        //
    }
}
