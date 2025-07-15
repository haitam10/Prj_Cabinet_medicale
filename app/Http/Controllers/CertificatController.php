<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\Patient;
use App\Models\User;

use Illuminate\Http\Request;

class CertificatController extends Controller
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
        'type' => 'required|string',
        'contenu' => 'required|string',
        'date_certificat' => 'required|date',
    ]);

    try {
        $patient = Patient::findOrFail($request->patient_id);
        $medecin = User::findOrFail($request->medecin_id);

        // Save to DB
        $certificat = new Certificat();
        $certificat->patient_id = $request->patient_id;
        $certificat->medecin_id = $request->medecin_id;
        $certificat->date_certificat = $request->date_certificat;
        $certificat->type = $request->type;
        $certificat->contenu = $request->contenu;
        $certificat->save();

        // Store for printing
        $documentData = [
            'patient_cin' => $patient->cin,
            'patient_nom' => $patient->nom,
            'medecin_nom' => $medecin->nom,
            'certificat_type' => $request->type,
            'contenu' => $request->contenu,
            'date' => $request->date_certificat,
        ];

        session(['print_certificat' => $documentData]);

        return redirect()->route('secretaire.certificats')
            ->with('success', 'Certificat généré avec succès!')
            ->with('print_document', true);

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Erreur lors de la génération du certificat: ' . $e->getMessage())
            ->withInput();
    }
}


    /**
     * Display the specified resource.
     */
    public function show(Certificat $certificat)
    {
        //
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
    public function update(Request $request, Certificat $certificat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificat $certificat)
    {
        //
    }
}
