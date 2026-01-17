<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Ensure Storage facade is imported
use Illuminate\Support\Facades\Auth;
use App\Models\DocModel; // This is the primary model for templates

class PapierController extends Controller
{
    /**
     * Display a listing of the resources (Certificat and Ordonnance models).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get authenticated doctor ID, default to 2 if not found for development/testing
        $doctorId = Auth::id() ?? 2;

        // Retrieve all certificat templates for the authenticated doctor
        $certifModels = DocModel::where('id_docteur', $doctorId)
                                ->where('document', 'certificat')
                                ->get();
                                
        // Retrieve all ordonnance templates for the authenticated doctor
        $ordonnModels = DocModel::where('id_docteur', $doctorId)
                                ->where('document', 'ordonnance')
                                ->get();
        
        // Retrieve cabinet information for the authenticated doctor
        $cabinetInfo = Cabinet::where('id_docteur', $doctorId)->first();

        // Pass data to the view
        return view('secretaire.papier', compact('cabinetInfo', 'certifModels', 'ordonnModels'));
    }
        
    /**
     * Update the selection status of a template (CertifDoc or OrdonDoc).
     * Note: This function uses CertifDoc and OrdonDoc models directly,
     * while create/update/delete/getTemplate use DocModel.
     * Ensure your application logic correctly distinguishes between these.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSelection(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $doctorId = Auth::id() ?? 2;
                
        if ($type === 'certif') {
            // Reset all certificates to not selected for this doctor
            // This assumes CertifDoc model has 'is_selected' and 'isSelected' columns
            \App\Models\CertifDoc::where('id_docteur', $doctorId)->update(['is_selected' => false, 'isSelected' => false]);
            // Set the selected one
            $certif = \App\Models\CertifDoc::find($id);
            if ($certif && $certif->id_docteur == $doctorId) {
                $certif->update(['is_selected' => true, 'isSelected' => true]);
            }
        } elseif ($type === 'ordonn') {
            // Reset all prescriptions to not selected for this doctor
            // This assumes OrdonDoc model has 'is_selected' and 'isSelected' columns
            \App\Models\OrdonDoc::where('id_docteur', $doctorId)->update(['is_selected' => false, 'isSelected' => false]);
            // Set the selected one
            $ordonn = \App\Models\OrdonDoc::find($id);
            if ($ordonn && $ordonn->id_docteur == $doctorId) {
                $ordonn->update(['is_selected' => true, 'isSelected' => true]);
            }
        }
                
        return response()->json(['success' => true]);
    }
        
    
    public function deleteTemplate(Request $request, $type, $id) // Correctly receive $type and $id as route parameters
    {
        $doctorId = Auth::id() ?? 2;
                
        // Find the template ensuring it belongs to the authenticated doctor and matches the type
        $template = DocModel::where('id', $id)
                            ->where('id_docteur', $doctorId)
                            ->where('document', $type) // Ensure type matches the 'document' column
                            ->first();

        // If template not found or unauthorized access
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template non trouvé ou non autorisé.'], 404);
        }

        try {
            // Delete logo file if it exists in storage/app/public/uploads
            if ($template->logo_file_path) {
                // 'public' disk refers to storage/app/public.
                // The logo_file_path stored is relative to this root (e.g., 'uploads/image.jpg').
                if (Storage::disk('public')->exists($template->logo_file_path)) {
                    Storage::disk('public')->delete($template->logo_file_path);
                }
            }
            
            // Delete the template record from the database
            $template->delete();

            return response()->json(['success' => true, 'message' => 'Template supprimé avec succès.']);
        } catch (\Exception $e) {
            // Handle any exceptions during deletion
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression du template: ' . $e->getMessage()], 500);
        }
    }
        
    
    public function getTemplate($type)
    {
        $doctorId = Auth::id() ?? 2;
                
        // Find the template by ID, doctor ID, and document type
        $template = DocModel::where('id_docteur', $doctorId)
                            ->where('document', $type)
                            ->first();
                            
        if ($template) {
            return response()->json($template);
        }
                
        return response()->json(['error' => 'Template not found or unauthorized'], 404);
    }
    
    public function getTemplates($type)
    {
        $doctorId = Auth::id() ?? 2;
                
        // Find the template by ID, doctor ID, and document type
        $templates = DocModel::where('id_docteur', $doctorId)
                            ->where('document', $type)
                            ->get();

        
        $formattedTemplates = $templates->map(function ($template) {
            return 
            [
            'id' => $template->id,
            'name' => $template->model_nom,
            'logo_file_path' => $template->logo_file_path,
            'descr_head' => $template->descr_head,
            'descr_body' => $template->descr_body,
            'descr_footer' => $template->descr_footer,
            'document' => $template->document,
            'is_selected' => $template->is_selected
        ];
        });

        return response()->json($formattedTemplates);
    }
    public function createTemplate(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'template_type' => 'required|in:certificat,ordonnance',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'header_description' => 'nullable|string',
            'body_description' => 'nullable|string',
            'footer_description' => 'nullable|string',
            // 'is_default' is not in the form, so removed from validation
        ]);

        $doctorId = Auth::id() ?? 2;
        $type = $request->input('template_type');
        $cabinet = Cabinet::where('id_docteur', $doctorId)->first();
        if (!$cabinet) {
            $cabinet = Cabinet::create([
                'id_docteur' => $doctorId,
                'nom_cabinet' => 'Nom par défaut',
                'addr_cabinet' => '',
                'tel_cabinet' => '',
                'descr_cabinet' => '',
            ]);
        }

        // Create a new DocModel instance
        $template = new DocModel();
        $template->id_docteur = $doctorId;
        $template->id_cabinet = $cabinet->id;
        $template->document = $type; // 'certificat' or 'ordonnance'
        $template->model_nom = $request->name;
        $template->descr_head = $request->header_description;
        $template->descr_body = $request->body_description;
        $template->descr_footer = $request->footer_description;

        // Handle logo upload
        if ($request->hasFile('logo_path')) {
            
            $path = $request->file('logo_path')->store('uploads', 'public');
            $template->logo_file_path = $path; // Store this path in the database column 'logo_file_path'
        }
        $template->save();

        return response()->json(['success' => true, 'message' => 'Template ajouté avec succès.']);
    }

    
    public function updateTemplate(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'id' => 'required|exists:doc_model,id', // Validate that ID exists in doc_models table
            'name' => 'required|string|max:255',
            'template_type' => 'required|in:certificat,ordonnance',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_description' => 'nullable|string',
            'body_description' => 'nullable|string',
            'footer_description' => 'nullable|string',
        ]);

        $doctorId = Auth::id() ?? 2;
        $templateId = $request->input('id');
        $type = $request->input('template_type');

        // Find the template ensuring it belongs to the authenticated doctor and matches the type
        $template = DocModel::where('id', $templateId)
                            ->where('id_docteur', $doctorId) 
                            ->where('document', $type)
                            ->firstOrFail(); // Throws 404 if not found

        // Update template fields
        $template->model_nom = $request->name;
        $template->descr_head = $request->header_description;
        $template->descr_body = $request->body_description;
        $template->descr_footer = $request->footer_description;

        // Handle logo update
        if ($request->hasFile('logo_path')) {
            // Delete old logo if it exists
            if ($template->logo_file_path && Storage::disk('public')->exists($template->logo_file_path)) {
                Storage::disk('public')->delete($template->logo_file_path);
            }
            // Store new logo in storage/app/public/uploads
            $path = $request->file('logo_path')->store('uploads', 'public');
            $template->logo_file_path = $path; // Consistent: save to logo_file_path
        }
        $template->save();

        return response()->json(['success' => true, 'message' => 'Template mis à jour avec succès.']);
    }
        
    public function store(Request $request)
    {
        // Validate only the required cabinet fields
        $request->validate([
            'nom_cabinet' => 'required|string|max:255',
            'addr_cabinet' => 'nullable|string|max:255',
            'tel_cabinet' => 'nullable|string|max:20',
            'desc_cabinet' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Get current doctor ID
            $doctorId = Auth::id() ?? 2;

            // Update or create cabinet record for the doctor
            Cabinet::updateOrCreate(
                ['id_docteur' => $doctorId],
                [
                    'nom_cabinet' => $request->nom_cabinet,
                    'addr_cabinet' => $request->addr_cabinet,
                    'tel_cabinet' => $request->tel_cabinet,
                    'descr_cabinet' => $request->desc_cabinet,
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Informations du cabinet sauvegardées avec succès!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

}
