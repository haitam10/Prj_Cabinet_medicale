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

        if (!$patient || !$medecin) {
            return response()->json(['error' => 'Patient ou médecin non trouvé pour cette ordonnance.'], 404);
        }

        // Fetch template specific to this doctor and document type 'ordonnance'
        $template = DocModel::where('id_docteur', $medecin->id)
                            ->where('document', 'ordonnance')
                            ->first();

        // Fetch cabinet associated with the doctor
        $cab = Cabinet::whereRaw("FIND_IN_SET(?, id_docteur)", [$medecin->id])->first();

        // Construct templateData, safely accessing properties with null coalescing and correct source
        $templateData = [
            'id' => $template->id ?? null, // Template ID
            'model_nom' => $template->model_nom ?? null, // Template name
            'logo_file_path' => $template->logo_file_path ?? null,
            'descr_head' => $template->descr_head ?? null, // Header description
            'descr_body' => $template->descr_body ?? null, // Body description
            'descr_footer' => $template->descr_footer ?? null, // Footer description
            'document' => 'certificat', // Document type
            'is_selected' => $template->is_selected ?? 0, // Template selection flag
        ];

        // Prepare the response structure for the certificate
        $documentData = [
            'certificat' => [
                'id' => $ordonnance->id,
                'contenu' => $ordonnance->instructions, // Assuming 'instructions' are the content
                'type' => 'Voyage', // Example, can change based on logic
                'date_certificat' => $ordonnance->date_ordonance,
                'duree_traitement' => $ordonnance->duree_traitement,
            ],
            'patient' => [
                'id' => $patient->id,
                'cin' => $patient->cin ?? 'N/A',
                'nom' => ($patient->nom ?? 'N/A') . ' ' . ($patient->prenom ?? ''),
                'telephone' => $patient->telephone ?? 'N/A',
                'date_naissance' => $patient->date_naissance ?? 'N/A',
                'sexe' => $patient->sexe ?? 'N/A',
            ],
            'medecin' => [
                'id' => $medecin->id ?? null,
                'nom' => $medecin->nom ?? 'N/A',
                'prenom' => $medecin->prenom ?? null,
                'email' => $medecin->email ?? 'N/A',
                'telephone' => $medecin->telephone ?? 'N/A',
                'specialite' => $medecin->specialite ?? 'N/A',
            ],
            'cabinet' => [
                'id' => $cab->id ?? null,
                'nom_cabinet' => $cab->nom_cabinet ?? 'N/A',
                'addr_cabinet' => $cab->addr_cabinet ?? 'N/A',
                'tel_cabinet' => $cab->tel_cabinet ?? 'N/A',
                'descr_cabinet' => $cab->descr_cabinet ?? 'N/A',
            ],
            'template' => $templateData, // Pass the safely constructed template data
        ];

        return response()->json($documentData);
    } catch (Exception $e) { // Catch more specific exceptions if needed
        // Log the error for debugging purposes
        Log::error("Erreur lors de la récupération des données de l'ordonnance: " . $e->getMessage(), ['exception' => $e]);
        
        return response()->json([
            'error' => 'Erreur lors de la récupération des données de l\'ordonnance.',
            'message' => $e->getMessage() // Provide the error message for debugging
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
