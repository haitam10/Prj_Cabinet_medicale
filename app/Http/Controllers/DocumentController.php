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
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showCerts(Request $request)
    {
        $documents = null;
                    
        try {
            $user = Auth::user();
            $search = $request->query('search');
            $medecinFilter = $request->query('medecin');
                            
            $certificatsQuery = Certificat::with(['patient', 'medecin']);
                            
            // Filtrer par médecin selon le rôle de l'utilisateur connecté
            if ($user->role === 'medecin') {
                $certificatsQuery->where('medecin_id', $user->id);
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $certificatsQuery->where('medecin_id', $user->medecin_id);
            } else {
                // Si pas de médecin assigné, aucun résultat
                $certificatsQuery->where('id', null);
            }
                            
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
                            
            // Filtrer les patients selon l'utilisateur connecté
            if ($user->role === 'medecin') {
                $patients = Patient::where('medecin_id', $user->id)->get();
                $medecins = User::where('id', $user->id)->get();
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $patients = Patient::where('medecin_id', $user->medecin_id)->get();
                $medecins = User::where('id', $user->medecin_id)->get();
            } else {
                $patients = collect();
                $medecins = collect();
            }
                        
        } catch (\Exception $e) {
            $documents = new LengthAwarePaginator(
                collect([]), 0, 10, 1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $patients = collect();
            $medecins = collect();
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

    /** 
     * Display ordonnances listing 
     */
    public function showOrds(Request $request)
    {
        $documents = null;
                    
        try {
            $user = Auth::user();
            $search = $request->query('search');
            $medecinFilter = $request->query('medecin');
                            
            $ordonnancesQuery = Ordonnance::with(['patient', 'medecin']);
                            
            // Filtrer par médecin selon le rôle de l'utilisateur connecté
            if ($user->role === 'medecin') {
                $ordonnancesQuery->where('medecin_id', $user->id);
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $ordonnancesQuery->where('medecin_id', $user->medecin_id);
            } else {
                // Si pas de médecin assigné, aucun résultat
                $ordonnancesQuery->where('id', null);
            }
                            
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

            // Filtrer les patients selon l'utilisateur connecté
            if ($user->role === 'medecin') {
                $patients = Patient::where('medecin_id', $user->id)->get();
                $medecins = User::where('id', $user->id)->get();
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $patients = Patient::where('medecin_id', $user->medecin_id)->get();
                $medecins = User::where('id', $user->medecin_id)->get();
            } else {
                $patients = collect();
                $medecins = collect();
            }
                        
        } catch (\Exception $e) {
            $documents = new LengthAwarePaginator(
                collect([]), 0, 10, 1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $patients = collect();
            $medecins = collect();
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
            $user = Auth::user();
            $search = $request->query('search');
            $medecinFilter = $request->query('medecin');
                            
            $remarquesQuery = Remarque::with(['patient', 'medecin']);
                            
            // Filtrer par médecin selon le rôle de l'utilisateur connecté
            if ($user->role === 'medecin') {
                $remarquesQuery->where('medecin_id', $user->id);
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $remarquesQuery->where('medecin_id', $user->medecin_id);
            } else {
                // Si pas de médecin assigné, aucun résultat
                $remarquesQuery->where('id', null);
            }
                            
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
                            
            // Filtrer les patients selon l'utilisateur connecté
            if ($user->role === 'medecin') {
                $patients = Patient::where('medecin_id', $user->id)->get();
                $medecins = User::where('id', $user->id)->get();
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $patients = Patient::where('medecin_id', $user->medecin_id)->get();
                $medecins = User::where('id', $user->medecin_id)->get();
            } else {
                $patients = collect();
                $medecins = collect();
            }
                        
        } catch (\Exception $e) {
            $documents = new LengthAwarePaginator(
                collect([]), 0, 10, 1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $patients = collect();
            $medecins = collect();
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