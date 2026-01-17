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
    public function showCerts(Request $request)
        {
            $documents = null;
            
            try {
                $search = $request->query('search');
                $medecinFilter = $request->query('medecin');
                
                $certificatsQuery = Certificat::with(['patient', 'medecin']);
                
                // Apply filters
                if ($search) {
                    $certificatsQuery->whereHas('patient', function ($q) use ($search) {
                        $q->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhere('cin', 'like', '%' . $search . '%');
                    });
                }
                
                if ($medecinFilter) {
                    $certificatsQuery->whereHas('medecin', function ($q) use ($medecinFilter) {
                        $q->where('nom', 'like', '%' . $medecinFilter . '%');
                    });
                }
                
                $certificats = $certificatsQuery->orderBy('date_certificat', 'desc')->get();
                
                // Map certificates to common format
                $mappedCertificats = $certificats->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'certificat',
                        'patient_cin' => $item->patient->cin ?? 'N/A',
                        'patient_nom' => ($item->patient->nom ?? 'N/A') . ' ' . ($item->patient->prenom ?? ''),
                        'medecin_nom' => $item->medecin->nom ?? 'N/A',
                        'certificat_type' => 'Certificat',
                        'contenu' => $item->contenu ?? null,
                        'date' => $item->date_certificat ?? null,
                    ];
                });
                
                // Manual pagination
                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $currentItems = $mappedCertificats->slice(($currentPage - 1) * $perPage, $perPage)->values();
                
                $documents = new LengthAwarePaginator(
                    $currentItems,
                    $mappedCertificats->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                $patients = Patient::all();
                $medecins = User::where('role', 'medecin')->get();
                
            } catch (\Exception $e) {
                $documents = new LengthAwarePaginator(
                    collect([]), 0, 10, 1,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                Log::error("Erreur dans DocumentController@showCerts: " . $e->getMessage());
            }
            
            if ($request->wantsJson()) {
               return response()->json([
                    'documents' => $documents,
                    'patients' => $patients,
                    'medecins' => $medecins,
                ]);
            }
            
            return view('secretaire.certificats', compact('documents','patients','medecins'));
        }


    public function showOrds(Request $request)
        {
            $documents = null;
            
            try {
                $search = $request->query('search');
                $medecinFilter = $request->query('medecin');
                
                $ordonnancesQuery = Ordonnance::with(['patient', 'medecin']);
                
                // Apply filters
                if ($search) {
                    $ordonnancesQuery->whereHas('patient', function ($q) use ($search) {
                        $q->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhere('cin', 'like', '%' . $search . '%');
                    });
                }
                
                if ($medecinFilter) {
                    $ordonnancesQuery->whereHas('medecin', function ($q) use ($medecinFilter) {
                        $q->where('nom', 'like', '%' . $medecinFilter . '%');
                    });
                }
                
                $ordonnances = $ordonnancesQuery->orderBy('date_ordonance', 'desc')->get();
                
                // Map ordonnances to common format
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
                
                // Manual pagination
                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $currentItems = $mappedOrdonnances->slice(($currentPage - 1) * $perPage, $perPage)->values();
                
                $documents = new LengthAwarePaginator(
                    $currentItems,
                    $mappedOrdonnances->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                $patients = Patient::all();
                $medecins = User::where('role', 'medecin')->get();
                
            } catch (\Exception $e) {
                $documents = new LengthAwarePaginator(
                    collect([]), 0, 10, 1,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                Log::error("Erreur dans DocumentController@showOrds: " . $e->getMessage());
            }
            
            if ($request->wantsJson()) {
               return response()->json([
                    'documents' => $documents,
                    'patients' => $patients,
                    'medecins' => $medecins,
                ]);
            }
            
            return view('secretaire.ordonnances', compact('documents','patients','medecins'));
        }

    /**
     * Display remarques listing
     */
    public function showRems(Request $request)
        {
            $documents = null;
            
            try {
                $search = $request->query('search');
                $medecinFilter = $request->query('medecin');
                
                $remarquesQuery = Remarque::with(['patient', 'medecin']);
                
                // Apply filters
                if ($search) {
                    $remarquesQuery->whereHas('patient', function ($q) use ($search) {
                        $q->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhere('cin', 'like', '%' . $search . '%');
                    });
                }
                
                if ($medecinFilter) {
                    $remarquesQuery->whereHas('medecin', function ($q) use ($medecinFilter) {
                        $q->where('nom', 'like', '%' . $medecinFilter . '%');
                    });
                }
                
                $remarques = $remarquesQuery->orderBy('date_remarque', 'desc')->get();
                
                // Map remarques to common format
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
                
                // Manual pagination
                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $currentItems = $mappedRemarques->slice(($currentPage - 1) * $perPage, $perPage)->values();
                
                $documents = new LengthAwarePaginator(
                    $currentItems,
                    $mappedRemarques->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                $patients = Patient::all();
                $medecins = User::where('role', 'medecin')->get();
                
            } catch (\Exception $e) {
                $documents = new LengthAwarePaginator(
                    collect([]), 0, 10, 1,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                Log::error("Erreur dans DocumentController@showRems: " . $e->getMessage());
            }
            
            if ($request->wantsJson()) {
               return response()->json([
                    'documents' => $documents,
                    'patients' => $patients,
                    'medecins' => $medecins,
                ]);
            }
            
            return view('secretaire.remarques', compact('documents','patients','medecins'));
        }

    
    

    }
