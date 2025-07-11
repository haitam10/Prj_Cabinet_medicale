<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\Ordonnance;
use App\Models\User; // Assuming User model is for doctors
use App\Models\Patient;
use App\Models\Remarque;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log; // Cette ligne est cruciale pour Intelephense

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $documents = null; // Initialiser à null pour être sûr

        try {
            // Récupérer les paramètres de filtre de la requête
            $search = $request->query('search');
            $typeFilter = $request->query('type');
            $medecinFilter = $request->query('medecin');

            // Requêtes pour chaque type de document
            $ordonnancesQuery = Ordonnance::with(['patient', 'medecin']);
            $certificatsQuery = Certificat::with(['patient', 'medecin']);
            $remarquesQuery = Remarque::with(['patient', 'medecin']);

            // Appliquer les filtres à chaque requête
            $applyFilters = function ($query) use ($search, $medecinFilter) {
                if ($search) {
                    $query->whereHas('patient', function ($q) use ($search) {
                        $q->where('nom', 'like', '%' . $search . '%')
                          ->orWhere('prenom', 'like', '%' . $search . '%')
                          ->orWhere('cin', 'like', '%' . $search . '%');
                    });
                }
                if ($medecinFilter) {
                    $query->whereHas('medecin', function ($q) use ($medecinFilter) {
                        $q->where('nom', 'like', '%' . $medecinFilter . '%');
                    });
                }
                return $query;
            };

            $ordonnances = $applyFilters($ordonnancesQuery)->get();
            $certificats = $applyFilters($certificatsQuery)->get();
            $remarques = $applyFilters($remarquesQuery)->get();

            // Mapper les documents dans un format commun et ajouter le type
            $mappedOrdonnances = $ordonnances->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'ordonnance',
                    'patient_cin' => $item->patient->cin ?? 'N/A',
                    'patient_nom' => ($item->patient->nom ?? 'N/A') . ' ' . ($item->patient->prenom ?? ''),
                    'medecin_nom' => $item->medecin->nom ?? 'N/A',
                    'instructions' => $item->instructions ?? null,
                    'medicaments' => $item->medicaments ?? null,
                    'duree_traitement' => $item->duree_traitement ?? null,
                    'date' => $item->date_ordonance ?? null,
                ];
            });

            $mappedCertificats = $certificats->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'certificat',
                    'patient_cin' => $item->patient->cin ?? 'N/A',
                    'patient_nom' => ($item->patient->nom ?? 'N/A') . ' ' . ($item->patient->prenom ?? ''),
                    'medecin_nom' => $item->medecin->nom ?? 'N/A',
                    'certificat_type' => $item->type ?? null,
                    'contenu' => $item->contenu ?? null,
                    'date' => $item->date_certificat ?? null,
                ];
            });

            $mappedRemarques = $remarques->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'remarque',
                    'patient_cin' => $item->patient->cin ?? 'N/A',
                    'patient_nom' => ($item->patient->nom ?? 'N/A') . ' ' . ($item->patient->prenom ?? ''),
                    'medecin_nom' => $item->medecin->nom ?? 'N/A',
                    'remarque' => $item->remarque ?? null,
                    'date' => $item->date_remarque ?? null,
                ];
            });

            // Combiner toutes les collections mappées
            $allDocuments = collect([]);
            if (!$typeFilter || $typeFilter === 'ordonnance') {
                $allDocuments = $allDocuments->concat($mappedOrdonnances);
            }
            if (!$typeFilter || $typeFilter === 'certificat') {
                $allDocuments = $allDocuments->concat($mappedCertificats);
            }
            if (!$typeFilter || $typeFilter === 'remarque') {
                $allDocuments = $allDocuments->concat($mappedRemarques);
            }

            // Trier la collection combinée par date (du plus récent au plus ancien)
            $sortedDocuments = $allDocuments->sortByDesc(function ($doc) {
                return Carbon::parse($doc['date'] ?? '1970-01-01');
            });

            // Pagination manuelle de la collection triée
            $perPage = 10;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $sortedDocuments->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $documents = new LengthAwarePaginator(
                $currentItems,
                $sortedDocuments->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

        } catch (\Exception $e) {
            // En cas d'erreur, initialiser $documents comme une collection vide paginée
            $documents = new LengthAwarePaginator(
                collect([]),
                0,
                10,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            Log::error("Erreur dans DocumentController@index: " . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json($documents);
        }

        return view('secretaire.documents', compact('documents'));
    }
}
