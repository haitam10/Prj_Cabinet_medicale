<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\CertifDoc;
use App\Models\DocModel;
use App\Models\Ordonnance;
use App\Models\OrdonDoc;
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
                                
            // Save to DB
            $ordonnance = new Ordonnance();
            $ordonnance->patient_id = $request->patient_id;
            $ordonnance->medecin_id = $request->medecin_id;
            $ordonnance->date_ordonance = $request->date_ordonnance;
            $ordonnance->medicaments = $request->medicaments;
            $ordonnance->instructions = $request->instructions;
            $ordonnance->duree_traitement = $request->duree_traitement;
            $ordonnance->save();

            // Get selected template for the doctor
            $template = OrdonDoc::where('id_docteur', $medecin->id)
                ->where('isSelected', true)
                ->first();

            // Create template object with default values
            $templateData = [
                'nom_cabinet' => $template->nom_cabinet ?? 'Cabinet Médical',
                'addr_cabinet' => $template->addr_cabinet ?? '123 Rue Médicale, Casablanca',
                'tel_cabinet' => $template->tel_cabinet ?? '0522-123456',
                'descOrdonn' => $template->descOrdonn ?? '',
                'logo_file_path' => $template->logo_file_path ?? null,
            ];

            // Prepare printable data with template details
            $documentData = [
                'patient_cin' => $patient->cin,
                'patient_nom' => $patient->nom,
                'medecin_id' => $medecin->id,
                'medecin_nom' => $medecin->nom,
                'medicaments' => $request->medicaments,
                'instructions' => $request->instructions,
                'duree_traitement' => $request->duree_traitement,
                'duree' => $request->duree_traitement, // for template compatibility
                'date' => $request->date_ordonnance,
                'template' => $templateData, // pass template data as array
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

    public function printOrdonnance($id)
    {
        try {
            $ordonnance = Ordonnance::with(['patient', 'medecin'])->findOrFail($id);

            // Get selected template for the doctor
            $template = DocModel::where('id_docteur', $ordonnance->medecin_id)
                ->where('isSelected', true)
                ->first();

            $cab = Cabinet::where('id_docteur', $ordonnance->medecin_id)
                ->first();

            // Create template object with default values
            $templateData = [
                'nom_cabinet' => $cab->nom_cabinet ?? '',
                'addr_cabinet' => $template->addr_cabinet ?? '',
                'tel_cabinet' => $template->tel_cabinet ?? '',
                'descOrdonn' => $template->descOrdonn ?? '',
                'logo_file_path' => $template->logo_file_path ?? null,
            ];

            // Prepare data for the template
            $data = [
                'id' => $ordonnance->id, // Ensure ID is included
                'patient_cin' => $ordonnance->patient->cin,
                'patient_nom' => $ordonnance->patient->nom,
                'medecin_id' => $ordonnance->medecin->id, // Include medecin_id
                'medecin_nom' => $ordonnance->medecin->nom,
                'medecin_mail' => $ordonnance->medecin->email,
                'medicaments' => $ordonnance->medicaments,
                'instructions' => $ordonnance->instructions,
                'duree' => $ordonnance->duree_traitement,
                'duree_traitement' => $ordonnance->duree_traitement, // Ensure both keys are present
                'date' => $ordonnance->date_ordonnance,
                'template' => (object) $templateData,
            ];

            return view('print.ordonnance', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'impression: ' . $e->getMessage());
        }
    }

    

   public function getOrdonnanceData($ordonnanceId)
{
    try {
        $ordonnance = Ordonnance::with(['patient', 'medecin'])->findOrFail($ordonnanceId);
        $patient = $ordonnance->patient;
        $medecin = $ordonnance->medecin;

        // Fetch Cabinet from `cabinets` table where id_docteur contains medecin->id
        $cabinet = Cabinet::whereRaw("FIND_IN_SET(?, id_docteur)", [$medecin->id])->first();

        // Fetch Template from `doc_models` table for this doctor and 'ordonnance'
        $template = DocModel::where('id_docteur', $medecin->id)
                            ->where('document', 'ordonnance')
                            ->first();

        return response()->json([
            'ordonnance' => [
                'id' => $ordonnance->id,
                'medicaments' => $ordonnance->medicaments,
                'instructions' => $ordonnance->instructions,
                'duree_traitement' => $ordonnance->duree_traitement,
                'date_ordonnance' => $ordonnance->date_ordonnance,
            ],

            'patient' => [
                'id' => $patient->id,
                'cin' => $patient->cin,
                'nom' => $patient->nom,
                'prenom' => $patient->prenom,
                'email' => $patient->email,
                'telephone' => $patient->telephone,
                'adresse' => $patient->adresse,
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