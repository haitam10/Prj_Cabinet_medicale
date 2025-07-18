<?php

namespace App\Http\Controllers;

use App\Models\Remarque;
use Illuminate\Http\Request;

class RemarqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Remarque::all());
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
            'date_remarque' => 'required|date',
            'remarque' => 'required|string',
        ]);
        $remarque = Remarque::create($validated);
        return response()->json(['message' => 'Remarque créée', 'remarque' => $remarque], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $remarque = Remarque::find($id);
        if (!$remarque) {
            return response()->json(['message' => 'Remarque non trouvée'], 404);
        }
        return response()->json($remarque);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Remarque $remarque)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $remarque = Remarque::find($id);
        if (!$remarque) {
            return response()->json(['message' => 'Remarque non trouvée'], 404);
        }
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'medecin_id' => 'sometimes|exists:users,id',
            'date_remarque' => 'sometimes|date',
            'remarque' => 'sometimes|string',
        ]);
        $remarque->update($validated);
        return response()->json(['message' => 'Remarque mise à jour', 'remarque' => $remarque]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $remarque = Remarque::find($id);
        if (!$remarque) {
            return response()->json(['message' => 'Remarque non trouvée'], 404);
        }
        $remarque->delete();
        return response()->json(['message' => 'Remarque supprimée']);
    }
}
