<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\DocModel;
use App\Models\CertifDoc;

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
     */
    public function updateSelection(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $doctorId = Auth::id() ?? 2;
                
        if ($type === 'certif') {
            // Reset all certificates to not selected for this doctor
            \App\Models\CertifDoc::where('id_docteur', $doctorId)->update(['is_selected' => false, 'isSelected' => false]);
            // Set the selected one
            $certif = \App\Models\CertifDoc::find($id);
            if ($certif && $certif->id_docteur == $doctorId) {
                $certif->update(['is_selected' => true, 'isSelected' => true]);
            }
        } elseif ($type === 'ordonn') {
            // Reset all prescriptions to not selected for this doctor
            \App\Models\OrdonDoc::where('id_docteur', $doctorId)->update(['is_selected' => false, 'isSelected' => false]);
            // Set the selected one
            $ordonn = \App\Models\OrdonDoc::find($id);
            if ($ordonn && $ordonn->id_docteur == $doctorId) {
                $ordonn->update(['is_selected' => true, 'isSelected' => true]);
            }
        }
                
        return response()->json(['success' => true]);
    }
        
    /**
     * Delete a template
     */
    public function deleteTemplate(Request $request, $type, $id)
    {
        $doctorId = Auth::id() ?? 2;
                
        // Find the template ensuring it belongs to the authenticated doctor and matches the type
        $template = DocModel::where('id', $id)
                            ->where('id_docteur', $doctorId)
                            ->where('document', $type)
                            ->first();

        // If template not found or unauthorized access
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template non trouvé ou non autorisé.'], 404);
        }

        try {
            // Delete logo file if it exists in storage/app/public/uploads
            if ($template->logo_file_path) {
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
        
    /**
     * Get a specific template
     */
    public function getTemplate($type, $id)
    {
        $doctorId = Auth::id() ?? 2;
                
        // Find the template by ID, doctor ID, and document type
        $template = DocModel::where('id', $id)
                            ->where('id_docteur', $doctorId)
                            ->where('document', $type)
                            ->first();
                            
        if ($template) {
            return response()->json($template);
        }
                
        return response()->json(['error' => 'Template not found or unauthorized'], 404);
    }
    
    /**
     * Get all templates of a specific type
     */
    public function getTemplates($type)
    {
        $doctorId = Auth::id() ?? 2;
                
        // Find templates by doctor ID and document type
        $templates = DocModel::where('id_docteur', $doctorId)
                            ->where('document', $type)
                            ->get();

        $formattedTemplates = $templates->map(function ($template) {
            return [
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

    /**
     * Create a new template
     */
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
        ]);

        $doctorId = Auth::id() ?? 2;
        $type = $request->input('template_type');
        $cabinet = Cabinet::where('id_docteur', $doctorId)->first();

        // Create a new DocModel instance
        $template = new DocModel();
        $template->id_docteur = $doctorId;
        $template->id_cabinet = $cabinet ? $cabinet->id : null;
        $template->document = $type;
        $template->model_nom = $request->name;
        $template->descr_head = $request->header_description;
        $template->descr_body = $request->body_description;
        $template->descr_footer = $request->footer_description;

        // Handle logo upload
        if ($request->hasFile('logo_path')) {
            $path = $request->file('logo_path')->store('uploads', 'public');
            $template->logo_file_path = $path;
        }
        
        $template->save();

        return response()->json(['success' => true, 'message' => 'Template ajouté avec succès.']);
    }

    /**
     * Update an existing template
     */
    public function updateTemplate(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'id' => 'required|exists:doc_model,id',
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
                            ->firstOrFail();

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
            // Store new logo
            $path = $request->file('logo_path')->store('uploads', 'public');
            $template->logo_file_path = $path;
        }
        
        $template->save();

        return response()->json(['success' => true, 'message' => 'Template mis à jour avec succès.']);
    }
        
    /**
     * Store cabinet information
     */
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