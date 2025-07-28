<?php

namespace App\Http\Controllers;

use App\Models\Disponibilite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rendezvous;
use App\Models\Facture;
use App\Models\Patient;
use App\Models\Paiement;
use Illuminate\Validation\Rule; // Importez Rule pour la validation conditionnelle
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Vérifier d'abord si l'utilisateur existe et a le bon statut
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Email ou mot de passe incorrect.',
            ])->withInput();
        }
        
        if ($user->statut !== 'actif') {
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte n\'est pas encore activé. Veuillez contacter l\'administrateur.',
            ])->withInput();
        }
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            return match ($user->role) {
                'admin' => redirect()->intended('/dashboard/admin'),
                'medecin' => redirect()->intended('secretaire/dashboard'), // Ou '/dashboard/medecin' si vous avez une route spécifique
                'secretaire' => redirect()->intended('secretaire/dashboard'),
                default => redirect()->intended('/'),
            };
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect.'], 401);
        }
        
        if ($user->statut !== 'actif') {
            return response()->json([
                'message' => 'Votre compte n\'est pas encore activé. Veuillez contacter l\'administrateur.'
            ], 403);
        }

        return response()->json(['message' => 'Connexion réussie.',
            'user' => $user,
            'redirect_url' => match ($user->role) {
                'admin' => '/dashboard/admin',
                'medecin' => '/dashboard/medecin',
                'secretaire' => '/dashboard/secretaire',
                default => null,
            }
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'cin' => 'required|string|unique:users,cin',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'date_naissance' => 'required|date',
            'sexe' => ['required', Rule::in(['Homme', 'Femme', 'Autre'])],
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'role' => ['required', Rule::in(['medecin', 'secretaire'])], 
            'specialite' => 'nullable|string|max:255', 
            'numero_adeli' => 'nullable|string|max:255', 
        ]);

        
        if ($validated['role'] === 'medecin') {
            $request->validate([
                'specialite' => 'required|string|max:255',
                'numero_adeli' => 'required|string|max:255',
            ]);
        }

        User::create([
            'cin' => $validated['cin'],
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'statut' => 'inactif', 
            'date_naissance' => $validated['date_naissance'],
            'sexe' => $validated['sexe'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'specialite' => $validated['role'] === 'medecin' ? ($validated['specialite'] ?? null) : null,
            'numero_adeli' => $validated['role'] === 'medecin' ? ($validated['numero_adeli'] ?? null) : null,
        ]);

        return redirect()->route('register.form')->with('success', 'Compte créé avec succès. Vous serez redirigé vers la page de connexion...');
    }

    public function apiRegister(Request $request)
    {
        $validated = $request->validate([
            'cin' => 'required|string|unique:users,cin',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'date_naissance' => 'required|date',
            'sexe' => ['required', Rule::in(['Homme', 'Femme', 'Autre'])],
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'role' => ['required', Rule::in(['medecin', 'secretaire'])], 
            'specialite' => 'nullable|string|max:255', 
            'numero_adeli' => 'nullable|string|max:255', 
        ]);

        if ($validated['role'] === 'medecin') {
            $request->validate([
                'specialite' => 'required|string|max:255',
                'numero_adeli' => 'required|string|max:255',
            ]);
        }

        $user = User::create([
            'cin' => $validated['cin'],
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'statut' => 'inactif', 
            'date_naissance' => $validated['date_naissance'],
            'sexe' => $validated['sexe'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'specialite' => $validated['role'] === 'medecin' ? ($validated['specialite'] ?? null) : null,
            'numero_adeli' => $validated['role'] === 'medecin' ? ($validated['numero_adeli'] ?? null) : null,
        ]);

        return response()->json(['message' => 'Compte créé avec succès.',
            'user' => $user
        ], 201);
    }

 public function secretDash(Request $request)
    {
        Log::info('secretDash called. wantsJson: ' . ($request->wantsJson() ? 'true' : 'false'));
        Log::info('Request headers: ' . json_encode($request->headers->all()));

        try {
            // Get all records for initial display
            $count_rvs = Rendezvous::count();
            $count_facts = Facture::count();
            $count_pats = Patient::count();
            $count_pais = Paiement::count();
            
            $latest_rvs = Rendezvous::latest()->take(5)->get();
            $latest_facs = Facture::latest()->take(5)->get();
            $latest_pats = Patient::latest()->take(5)->get();
            $latest_pais = Paiement::latest()->take(5)->get();
            


            $medecins = User::where('role', 'medecin')->get();
            $secretaires = User::where('role', 'secretaire')->get();
            $patients = Patient::all();
            $factures = Facture::all();

            $user = Auth::user();
            $disponibilites = collect();

                if ($user->role === 'medecin') {
                    $disponibilites = Disponibilite::where('medecin_id', $user->id)
                        ->where('date', '>=', now()->toDateString()) // Only future dates
                        ->orderBy('date', 'asc')
                        ->get();
                } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                    $disponibilites = Disponibilite::where('medecin_id', $user->medecin_id)
                        ->where('date', '>=', now()->toDateString()) // Only future dates
                        ->orderBy('date', 'asc')
                        ->get();
                }
            $query = Rendezvous::with(['patient', 'medecin', 'secretaire']); 
            $rendezvous = $query->orderBy('appointment_date', 'desc')
                           ->orderBy('appointment_time', 'desc')
                           ->paginate(10);

            $rendezvous->getCollection()->transform(function ($rdv) {
            $rdv->appointment_date_formatted = $rdv->appointment_date->format('Y-m-d');
            $rdv->appointment_time_formatted = substr($rdv->appointment_time, 0, 5);
            return $rdv;
        });
        $disponibilitesJS = $disponibilites->map(function($disp) {
            return [
                'date' => $disp->date,
                'heure_entree' => substr($disp->heure_entree, 0, 5),
                'heure_sortie' => substr($disp->heure_sortie, 0, 5)
            ];
        })->toArray();
           $existingAppointmentsJS = $rendezvous->map(function($rdv) {
                return [
                    'id' => $rdv->id,
                    'date' => $rdv->appointment_date->format('Y-m-d'),
                    'time' => substr($rdv->appointment_time, 0, 5),
                    'status' => $rdv->status
                ];
                })->toArray();



            $period = $request->input('period', 'day');
            $now = Carbon::now();

            // Initialize variables
            $rdv_chart_labels = [];
            $rdv_chart_confirmed_data = [];
            $rdv_chart_pending_data = [];
            $financial_chart_labels = [];
            $financial_chart_data = [];

            // Calculate date ranges and get filtered data
            switch ($period) {
                case 'day':
                    // Current period: Today
                    $currentStartDate = Carbon::today();
                    $currentEndDate = Carbon::today()->endOfDay();
                    
                    // Previous period: Yesterday (for comparison)
                    $previousStartDate = Carbon::yesterday();
                    $previousEndDate = Carbon::yesterday()->endOfDay();

                    // Chart data: Last 7 days
                    for ($i = 6; $i >= 0; $i--) {
                        $date = Carbon::today()->subDays($i);
                        $rdv_chart_labels[] = $date->format('D');
                        $financial_chart_labels[] = $date->format('D');

                        $confirmedCount = Rendezvous::whereDate('appointment_date', $date->toDateString())
                                                ->where('status', 'confirmed')
                                                ->count();
                        $pendingCount = Rendezvous::whereDate('appointment_date', $date->toDateString())
                                                ->where('status', 'pending')
                                                ->count();
                        $totalPayments = Paiement::whereDate('date_paiement', $date->toDateString())->count();

                        $rdv_chart_confirmed_data[] = $confirmedCount;
                        $rdv_chart_pending_data[] = $pendingCount;
                        $financial_chart_data[] = $totalPayments;
                    }
                    break;

                case 'month':
                    // Current period: This month
                    $currentStartDate = Carbon::now()->startOfMonth();
                    $currentEndDate = Carbon::now()->endOfMonth();
                    
                    // Previous period: Last month (for comparison)
                    $previousStartDate = Carbon::now()->subMonth()->startOfMonth();
                    $previousEndDate = Carbon::now()->subMonth()->endOfMonth();

                    // Chart data: Last 6 months
                    for ($i = 5; $i >= 0; $i--) {
                        $date = Carbon::now()->subMonths($i);
                        $rdv_chart_labels[] = $date->format('M Y');
                        $financial_chart_labels[] = $date->format('M Y');

                        $confirmedCount = Rendezvous::whereYear('appointment_date', $date->year)
                                                ->whereMonth('appointment_date', $date->month)
                                                ->where('status', 'confirmed')
                                                ->count();
                        $pendingCount = Rendezvous::whereYear('appointment_date', $date->year)
                                                ->whereMonth('appointment_date', $date->month)
                                                ->where('status', 'pending')
                                                ->count();
                        $totalPayments = Paiement::whereYear('date_paiement', $date->year)
                                                ->whereMonth('date_paiement', $date->month)
                                                ->count();

                        $rdv_chart_confirmed_data[] = $confirmedCount;
                        $rdv_chart_pending_data[] = $pendingCount;
                        $financial_chart_data[] = $totalPayments;
                    }
                    break;

                case 'year':
                    // Current period: This year
                    $currentStartDate = Carbon::now()->startOfYear();
                    $currentEndDate = Carbon::now()->endOfYear();
                    
                    // Previous period: Last year (for comparison)
                    $previousStartDate = Carbon::now()->subYear()->startOfYear();
                    $previousEndDate = Carbon::now()->subYear()->endOfYear();

                    // Chart data: Last 5 years
                    for ($i = 4; $i >= 0; $i--) {
                        $date = Carbon::now()->subYears($i);
                        $rdv_chart_labels[] = $date->format('Y');
                        $financial_chart_labels[] = $date->format('Y');

                        $confirmedCount = Rendezvous::whereYear('appointment_date', $date->year)
                                                ->where('status', 'confirmed')
                                                ->count();
                        $pendingCount = Rendezvous::whereYear('appointment_date', $date->year)
                                                ->where('status', 'pending')
                                                ->count();
                        $totalPayments = Paiement::whereYear('date_paiement', $date->year)->count();

                        $rdv_chart_confirmed_data[] = $confirmedCount;
                        $rdv_chart_pending_data[] = $pendingCount;
                        $financial_chart_data[] = $totalPayments;
                    }
                    break;

                default:
                    $currentStartDate = Carbon::minValue();
                    $currentEndDate = Carbon::maxValue();
                    $previousStartDate = Carbon::minValue();
                    $previousEndDate = Carbon::maxValue();
                    break;
            }

            // Get current period counts
            $count_rvs_filtered = Rendezvous::whereBetween('appointment_date', [$currentStartDate, $currentEndDate])->count();
            $count_facts_filtered = Facture::whereBetween('date', [$currentStartDate, $currentEndDate])->count();
            $count_pats_filtered = Patient::whereBetween('created_at', [$currentStartDate, $currentEndDate])->count();
            $count_pais_filtered = Paiement::whereBetween('date_paiement', [$currentStartDate, $currentEndDate])->count();

            // Get previous period counts for comparison
            $count_rvs_previous = Rendezvous::whereBetween('appointment_date', [$previousStartDate, $previousEndDate])->count();
            $count_facts_previous = Facture::whereBetween('date', [$previousStartDate, $previousEndDate])->count();
            $count_pats_previous = Patient::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
            $count_pais_previous = Paiement::whereBetween('date_paiement', [$previousStartDate, $previousEndDate])->count();

            // Calculate percentage differences (current vs previous period)
            $rvs_diff_percent = $count_rvs_previous > 0 ? (($count_rvs_filtered - $count_rvs_previous) / $count_rvs_previous) * 100 : ($count_rvs_filtered > 0 ? 100 : 0);
            $facts_diff_percent = $count_facts_previous > 0 ? (($count_facts_filtered - $count_facts_previous) / $count_facts_previous) * 100 : ($count_facts_filtered > 0 ? 100 : 0);
            $pats_diff_percent = $count_pats_previous > 0 ? (($count_pats_filtered - $count_pats_previous) / $count_pats_previous) * 100 : ($count_pats_filtered > 0 ? 100 : 0);
            $pais_diff_percent = $count_pais_previous > 0 ? (($count_pais_filtered - $count_pais_previous) / $count_pais_previous) * 100 : ($count_pais_filtered > 0 ? 100 : 0);

            // Calculate chart max values
            $rdv_max_val = 0;
            if (!empty($rdv_chart_confirmed_data) || !empty($rdv_chart_pending_data)) {
                $rdv_max_val = max(array_merge($rdv_chart_confirmed_data, $rdv_chart_pending_data));
            }
            $rdv_chart_max_y = $rdv_max_val > 0 ? $rdv_max_val * 1.1 : 10;

            $financial_max_val = 0;
            if (!empty($financial_chart_data)) {
                $financial_max_val = max($financial_chart_data);
            }
            $financial_chart_max_y = $financial_max_val > 0 ? $financial_max_val * 1.1 : 100;

            if ($request->wantsJson()) {
                $response_data = [
                    'count_rvs' => $count_rvs,
                    'count_facts' => $count_facts,
                    'count_pats' => $count_pats,
                    'count_pais' => $count_pais,
                    'latest_rvs' => $latest_rvs,
                    'latest_facs' => $latest_facs,
                    'latest_pats' => $latest_pats,
                    'latest_pais' => $latest_pais,
                    'medecins' => $medecins,
                    'secretaires' => $secretaires,
                    'patients' => $patients,
                    'factures' => $factures,
                    'disponibilites' => $disponibilites,
                    'existingAppointmentsJS' => $existingAppointmentsJS,        
                    'disponibilitesJS'      => $disponibilitesJS,  
                    'count_rvs_filtered' => $count_rvs_filtered,
                    'count_facts_filtered' => $count_facts_filtered,
                    'count_pats_filtered' => $count_pats_filtered,
                    'count_pais_filtered' => $count_pais_filtered,
                    'rvs_diff_percent' => round($rvs_diff_percent, 1),
                    'facts_diff_percent' => round($facts_diff_percent, 1),
                    'pats_diff_percent' => round($pats_diff_percent, 1),
                    'pais_diff_percent' => round($pais_diff_percent, 1),
                    'period' => $period,
                    'currentStartDate' => $currentStartDate,
                    'currentEndDate' => $currentEndDate,
                    'rdv_chart_labels' => $rdv_chart_labels,
                    'rdv_chart_confirmed_data' => $rdv_chart_confirmed_data,
                    'rdv_chart_pending_data' => $rdv_chart_pending_data,
                    'rdv_chart_max_y' => $rdv_chart_max_y,
                    'financial_chart_labels' => $financial_chart_labels,
                    'financial_chart_data' => $financial_chart_data,
                    'financial_chart_max_y' => $financial_chart_max_y,
                ];

                Log::info('Returning JSON response.');
                return response()->json($response_data);
            }

            Log::info('Returning regular view.');
            return view('secretaire.dashboard', compact(
                'count_rvs', 'count_facts', 'count_pats', 'count_pais',
                'latest_rvs', 'latest_facs', 'latest_pats', 'latest_pais',
                'medecins', 'secretaires', 'patients','factures','disponibilites','existingAppointmentsJS','disponibilitesJS',
                'count_rvs_filtered', 'count_facts_filtered', 'count_pats_filtered', 'count_pais_filtered',
                'rvs_diff_percent', 'facts_diff_percent', 'pats_diff_percent', 'pais_diff_percent',
                'rdv_chart_labels', 'rdv_chart_confirmed_data', 'rdv_chart_pending_data', 'rdv_chart_max_y',
                'financial_chart_labels', 'financial_chart_data', 'financial_chart_max_y', 'period',
            ));

        } catch (\Throwable $e) {
            Log::error('Error in secretDash: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

            if ($request->wantsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            } else {
                throw $e;
            }
        }
    }
}