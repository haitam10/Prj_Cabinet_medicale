<?php

namespace App\Http\Controllers;

use App\Models\Ordonnance;
use Illuminate\Http\Request;

class OrdonnanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Ordonnance::all());
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
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'date_ordonance' => 'required|date',
            'medicaments' => 'required|string',
            'duree_traitement' => 'required|string',
            'instructions' => 'required|string',
        ]);
        $ordonnance = Ordonnance::create($validated);
        return response()->json(['message' => 'Ordonnance créée', 'ordonnance' => $ordonnance], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ordonnance = Ordonnance::find($id);
        if (!$ordonnance) {
            return response()->json(['message' => 'Ordonnance non trouvée'], 404);
        }
        return response()->json($ordonnance);
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
    public function update(Request $request, $id)
    {
        $ordonnance = Ordonnance::find($id);
        if (!$ordonnance) {
            return response()->json(['message' => 'Ordonnance non trouvée'], 404);
        }
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'medecin_id' => 'sometimes|exists:users,id',
            'date_ordonance' => 'sometimes|date',
            'medicaments' => 'sometimes|string',
            'duree_traitement' => 'sometimes|string',
            'instructions' => 'sometimes|string',
        ]);
        $ordonnance->update($validated);
        return response()->json(['message' => 'Ordonnance mise à jour', 'ordonnance' => $ordonnance]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ordonnance = Ordonnance::find($id);
        if (!$ordonnance) {
            return response()->json(['message' => 'Ordonnance non trouvée'], 404);
        }
        $ordonnance->delete();
        return response()->json(['message' => 'Ordonnance supprimée']);
    }
}
