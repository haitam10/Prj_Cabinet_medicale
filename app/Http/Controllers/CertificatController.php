<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\CertifDoc;
use App\Models\Patient;
use App\Models\User;
use App\Models\DocModel;
use App\Models\Cabinet;
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
            'template_id' => 'nullable|string',
            'template_descr_head' => 'nullable|string',
            'template_descr_body' => 'nullable|string', 
            'template_descr_footer' => 'nullable|string',
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

            // Get selected template based on template_id from request
            $selectedTemplateId = $request->template_id;
            $template = null;

            if ($selectedTemplateId && $selectedTemplateId !== 'default') {
                // Try to get the specific template
                $template = DocModel::where('id_docteur', $medecin->id)
                    ->where('document', 'certificat')
                    ->where('id', $selectedTemplateId)
                    ->first();
            }

            // If no specific template found or default selected, use default
            if (!$template) {
                $template = (object) [
                    'id' => 'default',
                    'model_nom' => 'Modèle par défaut',
                    'logo_file_path' => 'uploads/okz6IeWL6Tc8ws7w6DzvCGeECccdMxOIYfeVUy0p.png',
                    'descr_head' => 'Je soussigné(e), atteste que le patient suivant :',
                    'descr_body' => 'présente un état nécessitant un arrêt temporaire de ses activités.',
                    'descr_footer' => 'Document remis à la personne concernée pour usage administratif.'
                ];
            }

            // Override template descriptions with user modifications if provided
            if ($request->filled('template_descr_head')) {
                $template->descr_head = $request->template_descr_head;
            }
            if ($request->filled('template_descr_body')) {
                $template->descr_body = $request->template_descr_body;
            }
            if ($request->filled('template_descr_footer')) {
                $template->descr_footer = $request->template_descr_footer;
            }
            
            // Get cabinet information
            $cabinet = Cabinet::whereRaw("FIND_IN_SET(?, id_docteur)", [$medecin->id])->first();

            // Prepare template data
            $templateData = [
                'id' => $template->id ?? 'default',
                'model_nom' => $template->model_nom ?? 'Modèle par défaut',
                'logo_file_path' => $template->logo_file_path ?? 'uploads/okz6IeWL6Tc8ws7w6DzvCGeECccdMxOIYfeVUy0p.png',
                'descr_head' => $template->descr_head ?? 'Je soussigné(e), atteste que le patient suivant :',
                'descr_body' => $template->descr_body ?? 'présente un état nécessitant un arrêt temporaire de ses activités.',
                'descr_footer' => $template->descr_footer ?? 'Document remis à la personne concernée pour usage administratif.'
            ];

            // Save template information in session for future use
            session(['certificat_template_' . $certificat->id => $templateData]);

            // Prepare printable data with all necessary information
            $documentData = [
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
                    'prenom' => $patient->prenom,
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
                    'nom_cabinet' => $cabinet->nom_cabinet ?? 'Cabinet Médical',
                    'addr_cabinet' => $cabinet->addr_cabinet ?? '123 Rue Médicale, Casablanca',
                    'tel_cabinet' => $cabinet->tel_cabinet ?? '0522-123456',
                    'descr_cabinet' => $cabinet->descr_cabinet ?? '',
                ],
                'template' => $templateData,
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
            $patient = $certificat->patient;
            $medecin = $certificat->medecin;

            // Try to get saved template information from session first
            $templateData = session('certificat_template_' . $certificatId);
            
            if (!$templateData) {
                // Fallback: Get selected template for the doctor - try to get doctor's template or use default
                $template = DocModel::where('id_docteur', $medecin->id)
                    ->where('document', 'certificat')
                    ->where('is_selected', true)
                    ->first();

                // If no selected template found, use default
                if (!$template) {
                    $template = (object) [
                        'id' => 'default',
                        'model_nom' => 'Modèle par défaut',
                        'logo_file_path' => 'uploads/okz6IeWL6Tc8ws7w6DzvCGeECccdMxOIYfeVUy0p.png',
                        'descr_head' => 'Je soussigné(e), atteste que le patient suivant :',
                        'descr_body' => 'présente un état nécessitant un arrêt temporaire de ses activités.',
                        'descr_footer' => 'Document remis à la personne concernée pour usage administratif.'
                    ];
                }

                // Create template object with default values
                $templateData = [
                    'id' => $template->id ?? 'default',
                    'model_nom' => $template->model_nom ?? 'Modèle par défaut',
                    'logo_file_path' => $template->logo_file_path ?? 'uploads/okz6IeWL6Tc8ws7w6DzvCGeECccdMxOIYfeVUy0p.png',
                    'descr_head' => $template->descr_head ?? 'Je soussigné(e), atteste que le patient suivant :',
                    'descr_body' => $template->descr_body ?? 'présente un état nécessitant un arrêt temporaire de ses activités.',
                    'descr_footer' => $template->descr_footer ?? 'Document remis à la personne concernée pour usage administratif.'
                ];
            }

            // Get cabinet information
            $cabinet = Cabinet::whereRaw("FIND_IN_SET(?, id_docteur)", [$medecin->id])->first();

            // Prepare the document data just like the store method
            $documentData = [
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
                    'prenom' => $patient->prenom,
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
                    'nom_cabinet' => $cabinet->nom_cabinet ?? 'Cabinet Médical',
                    'addr_cabinet' => $cabinet->addr_cabinet ?? '123 Rue Médicale, Casablanca',
                    'tel_cabinet' => $cabinet->tel_cabinet ?? '0522-123456',
                    'descr_cabinet' => $cabinet->descr_cabinet ?? '',
                ],
                'template' => $templateData,
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

            // Try to get saved template information from session first
            $savedTemplateData = session('certificat_template_' . $certificatId);
            
            if ($savedTemplateData) {
                // Use saved template data
                $templateData = $savedTemplateData;
            } else {
                // Fallback: Fetch Template from `doc_models` table for this doctor and 'certificat'
                $template = DocModel::where('id_docteur', $medecin->id)
                                    ->where('document', 'certificat')
                                    ->where('is_selected', true)
                                    ->first();

                // If no selected template found, use default
                if (!$template) {
                    $template = (object) [
                        'id' => 'default',
                        'model_nom' => 'Modèle par défaut',
                        'logo_file_path' => 'uploads/okz6IeWL6Tc8ws7w6DzvCGeECccdMxOIYfeVUy0p.png',
                        'descr_head' => 'Je soussigné(e), atteste que le patient suivant :',
                        'descr_body' => 'présente un état nécessitant un arrêt temporaire de ses activités.',
                        'descr_footer' => 'Document remis à la personne concernée pour usage administratif.'
                    ];
                }

                $templateData = [
                    'id' => $template->id ?? 'default',
                    'model_nom' => $template->model_nom ?? 'Modèle par défaut',
                    'logo_file_path' => $template->logo_file_path ?? 'uploads/okz6IeWL6Tc8ws7w6DzvCGeECccdMxOIYfeVUy0p.png',
                    'descr_head' => $template->descr_head ?? 'Je soussigné(e), atteste que le patient suivant :',
                    'descr_body' => $template->descr_body ?? 'présente un état nécessitant un arrêt temporaire de ses activités.',
                    'descr_footer' => $template->descr_footer ?? 'Document remis à la personne concernée pour usage administratif.',
                    'document' => $template->document ?? 'certificat',
                    'is_selected' => $template->is_selected ?? true,
                ];
            }

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
                    'prenom' => $patient->prenom,
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
                    'nom_cabinet' => $cabinet->nom_cabinet ?? 'Cabinet Médical',
                    'addr_cabinet' => $cabinet->addr_cabinet ?? '123 Rue Médicale, Casablanca',
                    'tel_cabinet' => $cabinet->tel_cabinet ?? '0522-123456',
                    'descr_cabinet' => $cabinet->descr_cabinet ?? '',
                ],

                'template' => $templateData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des données du certificat: ' . $e->getMessage()
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