<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use Illuminate\Http\Request;

class CertificatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Certificat::all());
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
            'date_certificat' => 'required|date',
            'type' => 'required|in:arrêt maladie,aptitude,autre',
            'contenu' => 'required|string',
        ]);
        $certificat = Certificat::create($validated);
        return response()->json(['message' => 'Certificat créé', 'certificat' => $certificat], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $certificat = Certificat::find($id);
        if (!$certificat) {
            return response()->json(['message' => 'Certificat non trouvé'], 404);
        }
        return response()->json($certificat);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificat $certificat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $certificat = Certificat::find($id);
        if (!$certificat) {
            return response()->json(['message' => 'Certificat non trouvé'], 404);
        }
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'medecin_id' => 'sometimes|exists:users,id',
            'date_certificat' => 'sometimes|date',
            'type' => 'sometimes|in:arrêt maladie,aptitude,autre',
            'contenu' => 'sometimes|string',
        ]);
        $certificat->update($validated);
        return response()->json(['message' => 'Certificat mis à jour', 'certificat' => $certificat]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $certificat = Certificat::find($id);
        if (!$certificat) {
            return response()->json(['message' => 'Certificat non trouvé'], 404);
        }
        $certificat->delete();
        return response()->json(['message' => 'Certificat supprimé']);
    }
}
