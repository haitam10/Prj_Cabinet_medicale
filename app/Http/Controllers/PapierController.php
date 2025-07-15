<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\CertifDoc;
use App\Models\OrdonDoc;

class PapierController extends Controller
{
    public function index()
    {
        return view('secretaire.papier');
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'certificat_template' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'ordonnance_template' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'certificat_examples' => 'nullable|array',
            'ordonnance_examples' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Get current doctor ID (assuming authenticated user is a doctor)
            $doctorId = Auth::id();

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            // Handle certificat template upload
            $certificatTemplatePath = null;
            if ($request->hasFile('certificat_template')) {
                $certificatTemplatePath = $request->file('certificat_template')->store('templates/certificats', 'public');
            }

            // Handle ordonnance template upload
            $ordonnanceTemplatePath = null;
            if ($request->hasFile('ordonnance_template')) {
                $ordonnanceTemplatePath = $request->file('ordonnance_template')->store('templates/ordonnances', 'public');
            }

            // First, set all existing records as not selected for this doctor
            CertifDoc::where('id_docteur', $doctorId)->update(['is_selected' => false]);
            OrdonDoc::where('id_docteur', $doctorId)->update(['isSelected' => false]);

            // Insert/Update CertifDoc record
            $certifDoc = CertifDoc::updateOrCreate(
                ['id_docteur' => $doctorId],
                [
                    'logo_file_path' => $logoPath,
                    'nom_cabinet' => 'Cabinet Médical', // You can make this dynamic
                    'addr_cabinet' => $request->adresse,
                    'tel_cabinet' => $request->telephone,
                    'desc_cabinet' => $request->email ? "Email: {$request->email}" : null,
                    'desc_certif' => $this->buildCertificatDescription($request->certificat_examples),
                    'is_selected' => true,
                ]
            );

            // Insert/Update OrdonDoc record
            $ordonDoc = OrdonDoc::updateOrCreate(
                ['id_docteur' => $doctorId],
                [
                    'logo_file_path' => $logoPath,
                    'addr_cabinet' => $request->adresse,
                    'tel_cabinet' => $request->telephone,
                    'descOrdonn' => $this->buildOrdonnanceDescription($request->ordonnance_examples),
                    'isSelected' => true,
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Configuration sauvegardée avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if there was an error
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            if ($certificatTemplatePath && Storage::disk('public')->exists($certificatTemplatePath)) {
                Storage::disk('public')->delete($certificatTemplatePath);
            }
            if ($ordonnanceTemplatePath && Storage::disk('public')->exists($ordonnanceTemplatePath)) {
                Storage::disk('public')->delete($ordonnanceTemplatePath);
            }

            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    /**
     * Build certificat description from selected examples
     */
    private function buildCertificatDescription($examples)
    {
        if (!$examples || !is_array($examples)) {
            return null;
        }

        $descriptions = [];
        foreach ($examples as $example) {
            switch ($example) {
                case 'ex1':
                    $descriptions[] = 'Certificat médical standard pour justification d\'absence';
                    break;
                case 'ex2':
                    $descriptions[] = 'Certificat médical pour activités sportives';
                    break;
                default:
                    $descriptions[] = "Certificat exemple: {$example}";
            }
        }

        return implode('; ', $descriptions);
    }

    /**
     * Build ordonnance description from selected examples
     */
    private function buildOrdonnanceDescription($examples)
    {
        if (!$examples || !is_array($examples)) {
            return null;
        }

        $descriptions = [];
        foreach ($examples as $example) {
            switch ($example) {
                case 'ex1':
                    $descriptions[] = 'Ordonnance standard avec posologie détaillée';
                    break;
                case 'ex2':
                    $descriptions[] = 'Ordonnance pour traitement chronique';
                    break;
                default:
                    $descriptions[] = "Ordonnance exemple: {$example}";
            }
        }

        return implode('; ', $descriptions);
    }

    /**
     * Get current configuration for a doctor
     */
    public function getConfiguration()
    {
        $doctorId = Auth::id();
        
        $certifDoc = CertifDoc::where('id_docteur', $doctorId)
                             ->where('is_selected', true)
                             ->first();
                             
        $ordonDoc = OrdonDoc::where('id_docteur', $doctorId)
                           ->where('isSelected', true)
                           ->first();

        return response()->json([
            'certificat' => $certifDoc,
            'ordonnance' => $ordonDoc
        ]);
    }
}