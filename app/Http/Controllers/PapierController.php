<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\DocModel;
use App\Models\CertifDoc;
use App\Models\OrdonDoc;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PapierController extends Controller
{
    /**
     * Display a listing of the resources (Certificat and Ordonnance models).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get authenticated doctor ID, default to 2 if not found for development /testing
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
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des templates: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement de la page.');
        }
    }
        
    /**
     * Update the selection status of a template (CertifDoc or OrdonDoc).
     */
    public function updateSelection(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:certif,ordonn',
                'id' => 'required|integer|exists:doc_model,id'
            ]);

            $type = $request->input('type');
            $id = $request->input('id');
            $doctorId = Auth::id() ?? 2;
                    
            if ($type === 'certif') {
                // Reset all certificates to not selected for this doctor
                CertifDoc::where('id_docteur', $doctorId)->update(['is_selected' => false, 'isSelected' => false]);
                // Set the selected one
                $certif = CertifDoc::find($id);
                if ($certif && $certif->id_docteur == $doctorId) {
                    $certif->update(['is_selected' => true, 'isSelected' => true]);
                }
            } elseif ($type === 'ordonn') {
                // Reset all prescriptions to not selected for this doctor
                OrdonDoc::where('id_docteur', $doctorId)->update(['is_selected' => false, 'isSelected' => false]);
                // Set the selected one
                $ordonn = OrdonDoc::find($id);
                if ($ordonn && $ordonn->id_docteur == $doctorId) {
                    $ordonn->update(['is_selected' => true, 'isSelected' => true]);
                }
            }
                    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la sélection: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }
        
    /**
     * Delete a template
     */
    public function deleteTemplate(Request $request, $type, $id)
    {
        try {
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
            Log::error('Erreur lors de la suppression du template: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression du template.'], 500);
        }
    }
        
    /**
     * Get a specific template
     */
    public function getTemplate($type, $id)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du template: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue.'], 500);
        }
    }
    
    /**
     * Get all templates of a specific type
     */
    public function getTemplates($type)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des templates: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Process and optimize uploaded logo
     */
    private function processLogo($file)
    {
        try {
            // Generate unique filename with timestamp
            $filename = time() . '_' . uniqid() . '.png';
            $path = 'uploads/' . $filename;
            
            // Create the uploads directory if it doesn't exist
            if (!Storage::disk('public')->exists('uploads')) {
                Storage::disk('public')->makeDirectory('uploads');
            }
            
            // Get the full path for storage
            $fullPath = storage_path('app/public/' . $path);
            
            // Process image with Intervention Image v3 (if available) or use basic file operations
            if (class_exists('Intervention\Image\ImageManager')) {
                // Use Intervention Image v3 for better processing
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());
                
                // Resize to logo size (150x150 max, maintaining aspect ratio)
                $image->scaleDown(150, 150);
                
                // Create a transparent background and save as PNG
                // PNG format automatically preserves transparency
                $image->toPng()->save($fullPath);
            } else {
                // Fallback: simple file move
                $file->storeAs('uploads', $filename, 'public');
            }
            
            // Verify the file was created successfully
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Logo processing error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new template
     */
    public function createTemplate(Request $request)
    {
        try {
            // Validate incoming request data
            $request->validate([
                'name' => 'required|string|max:255',
                'template_type' => 'required|in:certificat,ordonnance',
                'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', 
                'header_description' => 'nullable|string',
                'body_description' => 'nullable|string',
                'footer_description' => 'nullable|string',
            ]);

            $doctorId = Auth::id() ?? 2;
            $type = $request->input('template_type');
            $cabinet = Cabinet::where('id_docteur', $doctorId)->first();

            DB::beginTransaction();

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
                $logoPath = $this->processLogo($request->file('logo_path'));
                
                if ($logoPath) {
                    $template->logo_file_path = $logoPath;
                } else {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Erreur lors du traitement du logo.'], 500);
                }
            }
            
            $template->save();
            
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Template ajouté avec succès.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Données invalides.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded file if template save fails
            if (isset($logoPath) && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            
            Log::error('Erreur lors de la création du template: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la création du template.'], 500);
        }
    }

    /**
     * Update an existing template
     */
    public function updateTemplate(Request $request)
    {
        try {
            // Validate incoming request data
            $request->validate([
                'id' => 'required|exists:doc_model,id',
                'name' => 'required|string|max:255',
                'template_type' => 'required|in:certificat,ordonnance',
                'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                'header_description' => 'nullable|string',
                'body_description' => 'nullable|string',
                'footer_description' => 'nullable|string',
            ]);

            $doctorId = Auth::id() ?? 2;
            $templateId = $request->input('id');
            $type = $request->input('template_type');

            DB::beginTransaction();

            // Find the template ensuring it belongs to the authenticated doctor and matches the type
            $template = DocModel::where('id', $templateId)
                                ->where('id_docteur', $doctorId) 
                                ->where('document', $type)
                                ->firstOrFail();

            // Store old logo path for cleanup
            $oldLogoPath = $template->logo_file_path;

            // Update template fields
            $template->model_nom = $request->name;
            $template->descr_head = $request->header_description;
            $template->descr_body = $request->body_description;
            $template->descr_footer = $request->footer_description;

            // Handle logo update
            if ($request->hasFile('logo_path')) {
                $newLogoPath = $this->processLogo($request->file('logo_path'));
                
                if ($newLogoPath) {
                    $template->logo_file_path = $newLogoPath;
                    
                    // Delete old logo after successful upload
                    if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                        Storage::disk('public')->delete($oldLogoPath);
                    }
                } else {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Erreur lors du traitement du nouveau logo.'], 500);
                }
            }
            
            $template->save();
            
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Template mis à jour avec succès.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Données invalides.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up new uploaded file if update fails
            if (isset($newLogoPath) && Storage::disk('public')->exists($newLogoPath)) {
                Storage::disk('public')->delete($newLogoPath);
            }
            
            Log::error('Erreur lors de la mise à jour du template: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour du template.'], 500);
        }
    }
        
    /**
     * Store cabinet information
     */
    public function store(Request $request)
    {
        try {
            // Validate only the required cabinet fields
            $request->validate([
                'nom_cabinet' => 'required|string|max:255',
                'addr_cabinet' => 'nullable|string|max:255',
                'tel_cabinet' => 'nullable|string|max:20',
                'desc_cabinet' => 'nullable|string|max:1000',
            ]);

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la sauvegarde du cabinet: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde des informations du cabinet.');
        }
    }
}