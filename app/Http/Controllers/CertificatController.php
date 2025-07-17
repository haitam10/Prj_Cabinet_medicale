<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\CertifDoc;
use App\Models\Patient;
use App\Models\User;

use App\Models\Cabinet;
use App\Models\DocModel;
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
            'contenu' => 'string',
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

            // Get selected template for the doctor
            $template = DocModel::where('id_docteur', $medecin->id)
                ->where('document','certificat')
                ->first();

            // Create template object with default values
            $templateData = [
                'nom_cabinet' => $template->nom_cabinet ?? 'Cabinet Médical',
                'addr_cabinet' => $template->addr_cabinet ?? '123 Rue Médicale, Casablanca',
                'tel_cabinet' => $template->tel_cabinet ?? '0522-123456',
                'desc_cabinet' => $template->desc_cabinet ?? '',
                'logo_file_path' => $template->logo_file_path ?? null,
            ];

            // Prepare printable data with template details
            $documentData = [
                'patient_cin' => $patient->cin,
                'patient_nom' => $patient->nom,
                'medecin_id' => $medecin->id,
                'medecin_nom' => $medecin->nom,
                'type' => $request->type,
                'contenu' => $request->contenu,
                'description' => $request->contenu,
                'date' => $request->date_certificat,
                'template' => $templateData, // pass template data as array
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

    public function show($certificatId)
    {
        try {
            // Retrieve the certificat from the database
            $certificat = Certificat::findOrFail($certificatId);
            // Fetch the related patient and doctor data
            $patient = $certificat->patient; // assuming relationship is set on Certificat model
            $medecin = $certificat->medecin; // assuming relationship is set on Certificat model

            // Get selected template for the doctor
            $template = CertifDoc::where('id_docteur', $medecin->id)
                ->where('is_selected', true)
                ->first();

            // Create template object with default values
            $templateData = [
                'nom_cabinet' => $template->nom_cabinet ?? 'Bureau Médical',
                'addr_cabinet' => $template->addr_cabinet ?? '123 Rue Médicale, Casablanca',
                'tel_cabinet' => $template->tel_cabinet ?? '0522-123456',
                'desc_cabinet' => $template->desc_cabinet ?? '',
                'logo_file_path' => $template->logo_file_path ?? null,
            ];

            // Prepare the document data just like the store method
            $documentData = [
                'id' => $certificat->id, // Include certificate ID
                'patient_cin' => $patient->cin,
                'patient_nom' => $patient->nom,
                'medecin_id' => $medecin->id,
                'medecin_nom' => $medecin->nom,
                'type' => $certificat->type,
                'contenu' => $certificat->contenu,
                'description' => $certificat->contenu, // or modify as per your requirement
                'date' => $certificat->date_certificat,
                'certificat_type' => $certificat->type, // Ensure this matches what's used in blade
                'template' => $templateData, // pass template data as array
            ];

            // You can also prepare the document to be printed (for example, as in the store method)
            session(['print_certificat' => $documentData]);

            return view('secretaire.certificat.show', compact('documentData'))
                ->with('print_document', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la récupération du certificat: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function getCertificatData($certificatId)
    {
        try 
        {
        $certificat = Certificat::with(['patient', 'medecin'])->findOrFail($certificatId);
        $patient = $certificat->patient;
        $medecin = $certificat->medecin;

        // Fetch Cabinet from `cabinets` table where id_docteur contains medecin->id
        $cabinet = Cabinet::whereRaw("FIND_IN_SET(?, id_docteur)", [$medecin->id])->first();

        // Fetch Template from `doc_models` table for this doctor and 'ordonnance'
        $template = DocModel::where('id_docteur', $medecin->id)
                            ->where('document', 'certificat')
                            ->first();

        return response()->json([
            'certificat' => [
                'id' => $certificat->id,
                'contenu' => $certificat->contenu,
                'type' => $certificat->type,
                'date_certificat' => $certificat->date_certificat,
            ],

            'patient' => [
                'id' => $patient->id,
                'cin' => $patient->cin,
                'nom' => $patient->nom,
                'telephone' => $patient->contact,
                'date_naissance' => $patient->date_naissance,
                'sexe' => $patient->sexe,
            ],

            'medecin' => [
                'id' => $medecin->id,
                'nom' => $medecin->nom,
                'prenom' => $medecin->prenom,
                'email' => $medecin->email,
                'telephone' => $medecin->telephone,
                'specialite' => $medecin->specialite,
            ],

            'cabinet' => [
                'id' => $cabinet->id ?? null,
                'nom_cabinet' => $cabinet->nom_cabinet ?? null,
                'addr_cabinet' => $cabinet->addr_cabinet ?? null,
                'tel_cabinet' => $cabinet->tel_cabinet ?? null,
                'descr_cabinet' => $cabinet->descr_cabinet ?? null,
            ],

            'template' => [
                'id' => $template->id ?? null,
                'model_nom' => $template->model_nom ?? null,
                'logo_file_path' => $template->logo_file_path ?? null,
                'descr_head' => $template->descr_head ?? null,
                'descr_body' => $template->descr_body ?? null,
                'descr_footer' => $template->descr_footer ?? null,
                'document' => $template->document ?? null,
                'is_selected' => $template->is_selected ?? null,
            ],
        ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Erreur lors de la récupération des données de l\'ordonnance: ' . $e->getMessage()
                ], 500);
            }
    }

    
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
