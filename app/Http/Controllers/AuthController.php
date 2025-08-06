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
use App\Models\Cabinet;
use Illuminate\Validation\Rule;
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
                'medecin' => redirect()->intended('secretaire/dashboard'),
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

        return response()->json([
            'message' => 'Connexion réussie.',
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

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'user' => $user
        ], 201);
    }

    public function secretDash(Request $request)
    {
        Log::info('secretDash called. wantsJson: ' . ($request->wantsJson() ? 'true' : 'false'));
        Log::info('Request headers: ' . json_encode($request->headers->all()));

        try {
            $user = Auth::user();

            // Déterminer l'ID du médecin à filtrer selon le rôle de l'utilisateur
            $medecinId = null;
            if ($user->role === 'medecin') {
                $medecinId = $user->id;
            } elseif ($user->role === 'secretaire') {
                $medecinId = $user->medecin_id;
            }

            Log::info("Medecin ID determined: " . $medecinId);

            // Si pas de médecin ID trouvé, créer des collections vides
            if (!$medecinId) {
                $count_rvs = 0;
                $count_facts = 0;
                $count_pats = 0;
                $count_pais = 0;
                $latest_rvs = collect();
                $latest_facs = collect();
                $latest_pats = collect();
                $latest_pais = collect();
                $medecins = collect();
                $secretaires = collect();
                $patients = collect();
                $factures = collect(); // Collection vide si pas de médecin
                $disponibilites = collect();
            } else {
                // Filtrer toutes les données par medecin_id
                $count_rvs = Rendezvous::where('medecin_id', $medecinId)->count();
                $count_facts = Facture::where('medecin_id', $medecinId)->count();
                $count_pats = Patient::where('medecin_id', $medecinId)->count();
                $count_pais = Paiement::whereHas('facture', function($query) use ($medecinId) {
                    $query->where('medecin_id', $medecinId);
                })->count();

                $latest_rvs = Rendezvous::where('medecin_id', $medecinId)->latest()->take(5)->get();
                $latest_facs = Facture::where('medecin_id', $medecinId)->latest()->take(5)->get();
                $latest_pats = Patient::where('medecin_id', $medecinId)->latest()->take(5)->get();
                $latest_pais = Paiement::whereHas('facture', function($query) use ($medecinId) {
                    $query->where('medecin_id', $medecinId);
                })->latest()->take(5)->get();

                // Filtrer les médecins et secrétaires selon le rôle
                if ($user->role === 'medecin') {
                    $medecins = User::where('id', $user->id)->where('role', 'medecin')->get();
                    $secretaires = User::where('role', 'secretaire')->where('medecin_id', $user->id)->get();
                } elseif ($user->role === 'secretaire') {
                    $medecins = User::where('id', $medecinId)->where('role', 'medecin')->get();
                    $secretaires = User::where('id', $user->id)->where('role', 'secretaire')->get();
                } else {
                    $medecins = collect();
                    $secretaires = collect();
                }

                $patients = Patient::where('medecin_id', $medecinId)->get();

                // CORRECTION CRUCIALE: Appliquer la même logique que dans PaiementController
                // pour éviter les paiements en double
                $facturesQuery = Facture::with('patient')
                    ->where('statut', '!=', 'payée')
                    ->whereNotIn('id', function($query) {
                        $query->select('facture_id')
                              ->from('paiements')
                              ->where('statut', 'paye');
                    })
                    ->where('medecin_id', $medecinId);

                $factures = $facturesQuery->get();

                $disponibilites = Disponibilite::where('medecin_id', $medecinId)
                    ->where('date', '>=', now()->toDateString())
                    ->orderBy('date', 'asc')
                    ->get();
            }

            $query = Rendezvous::with(['patient', 'medecin', 'secretaire']);

            // Filtrer les rendez-vous selon le rôle
            if ($medecinId) {
                $query->where('medecin_id', $medecinId);
            } else {
                $query->where('id', null); // Pas de résultats si pas de médecin associé
            }

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

            // NEW: Initialize payment line chart variables
            $payment_line_labels = [];
            $payment_line_paye_data = [];
            $payment_line_attente_data = [];
            $payment_line_echoue_data = [];

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
                        $payment_line_labels[] = $date->format('D');

                        if ($medecinId) {
                            $confirmedCount = Rendezvous::where('medecin_id', $medecinId)
                                ->whereDate('appointment_date', $date->toDateString())
                                ->where('status', 'confirmed')
                                ->count();

                            $pendingCount = Rendezvous::where('medecin_id', $medecinId)
                                ->whereDate('appointment_date', $date->toDateString())
                                ->where('status', 'pending')
                                ->count();

                            $totalPayments = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereDate('date_paiement', $date->toDateString())
                            ->count();

                            // NEW: Payment line chart data
                            $payeCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereDate('date_paiement', $date->toDateString())
                            ->where('statut', 'paye')
                            ->count();

                            $attenteCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereDate('date_paiement', $date->toDateString())
                            ->where('statut', 'en_attente')
                            ->count();

                            $echoueCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereDate('date_paiement', $date->toDateString())
                            ->where('statut', 'echoue')
                            ->count();
                        } else {
                            $confirmedCount = 0;
                            $pendingCount = 0;
                            $totalPayments = 0;
                            $payeCount = 0;
                            $attenteCount = 0;
                            $echoueCount = 0;
                        }

                        $rdv_chart_confirmed_data[] = $confirmedCount;
                        $rdv_chart_pending_data[] = $pendingCount;
                        $financial_chart_data[] = $totalPayments;
                        $payment_line_paye_data[] = $payeCount;
                        $payment_line_attente_data[] = $attenteCount;
                        $payment_line_echoue_data[] = $echoueCount;
                    }

                    // For charges chart, use current month data when period is day
                    $chargesStartDate = Carbon::now()->startOfMonth();
                    $chargesEndDate = Carbon::now()->endOfMonth();
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
                        $payment_line_labels[] = $date->format('M Y');

                        if ($medecinId) {
                            $confirmedCount = Rendezvous::where('medecin_id', $medecinId)
                                ->whereYear('appointment_date', $date->year)
                                ->whereMonth('appointment_date', $date->month)
                                ->where('status', 'confirmed')
                                ->count();

                            $pendingCount = Rendezvous::where('medecin_id', $medecinId)
                                ->whereYear('appointment_date', $date->year)
                                ->whereMonth('appointment_date', $date->month)
                                ->where('status', 'pending')
                                ->count();

                            $totalPayments = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->whereMonth('date_paiement', $date->month)
                            ->count();

                            // NEW: Payment line chart data
                            $payeCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->whereMonth('date_paiement', $date->month)
                            ->where('statut', 'paye')
                            ->count();

                            $attenteCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->whereMonth('date_paiement', $date->month)
                            ->where('statut', 'en_attente')
                            ->count();

                            $echoueCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->whereMonth('date_paiement', $date->month)
                            ->where('statut', 'echoue')
                            ->count();
                        } else {
                            $confirmedCount = 0;
                            $pendingCount = 0;
                            $totalPayments = 0;
                            $payeCount = 0;
                            $attenteCount = 0;
                            $echoueCount = 0;
                        }

                        $rdv_chart_confirmed_data[] = $confirmedCount;
                        $rdv_chart_pending_data[] = $pendingCount;
                        $financial_chart_data[] = $totalPayments;
                        $payment_line_paye_data[] = $payeCount;
                        $payment_line_attente_data[] = $attenteCount;
                        $payment_line_echoue_data[] = $echoueCount;
                    }

                    // For charges chart, use current month data
                    $chargesStartDate = Carbon::now()->startOfMonth();
                    $chargesEndDate = Carbon::now()->endOfMonth();
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
                        $payment_line_labels[] = $date->format('Y');

                        if ($medecinId) {
                            $confirmedCount = Rendezvous::where('medecin_id', $medecinId)
                                ->whereYear('appointment_date', $date->year)
                                ->where('status', 'confirmed')
                                ->count();

                            $pendingCount = Rendezvous::where('medecin_id', $medecinId)
                                ->whereYear('appointment_date', $date->year)
                                ->where('status', 'pending')
                                ->count();

                            $totalPayments = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->count();

                            // NEW: Payment line chart data
                            $payeCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->where('statut', 'paye')
                            ->count();

                            $attenteCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->where('statut', 'en_attente')
                            ->count();

                            $echoueCount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                                $query->where('medecin_id', $medecinId);
                            })
                            ->whereYear('date_paiement', $date->year)
                            ->where('statut', 'echoue')
                            ->count();
                        } else {
                            $confirmedCount = 0;
                            $pendingCount = 0;
                            $totalPayments = 0;
                            $payeCount = 0;
                            $attenteCount = 0;
                            $echoueCount = 0;
                        }

                        $rdv_chart_confirmed_data[] = $confirmedCount;
                        $rdv_chart_pending_data[] = $pendingCount;
                        $financial_chart_data[] = $totalPayments;
                        $payment_line_paye_data[] = $payeCount;
                        $payment_line_attente_data[] = $attenteCount;
                        $payment_line_echoue_data[] = $echoueCount;
                    }

                    // For charges chart, use current year data
                    $chargesStartDate = Carbon::now()->startOfYear();
                    $chargesEndDate = Carbon::now()->endOfYear();
                    break;

                default:
                    $currentStartDate = Carbon::minValue();
                    $currentEndDate = Carbon::maxValue();
                    $previousStartDate = Carbon::minValue();
                    $previousEndDate = Carbon::maxValue();
                    $chargesStartDate = Carbon::now()->startOfMonth();
                    $chargesEndDate = Carbon::now()->endOfMonth();
                    break;
            }

            // CHARGES CALCULATION - VERSION CORRIGÉE
            $charges_data = [];
            $charges_labels = [];
            $charges_colors = [];
            $charges_hover_data = [];
            $total_paid_amount = 0;

            Log::info("Starting charges calculation for medecin_id: " . $medecinId);

            // Toujours essayer de calculer les charges si on a un médecin
            if ($medecinId) {
                // 1. Vérifier si un cabinet existe pour ce médecin
                $cabinet = Cabinet::where('id_docteur', $medecinId)->first();
                Log::info("Cabinet found: " . ($cabinet ? 'Yes' : 'No'));
                
                if ($cabinet) {
                    Log::info("Cabinet charges data: " . json_encode($cabinet->charges));
                }

                // 2. Calculer le montant total payé (pour les pourcentages)
                $total_paid_amount = Paiement::whereHas('facture', function($query) use ($medecinId) {
                    $query->where('medecin_id', $medecinId);
                })
                ->whereBetween('date_paiement', [$chargesStartDate, $chargesEndDate])
                ->where('statut', 'paye')
                ->sum('montant');

                Log::info("Total paid amount: " . $total_paid_amount);

                // 3. Initialiser les types de charges
                $chargesSums = [
                    'Salaires' => 0,
                    'Cabinet charges' => 0,
                    'Crédits' => 0,
                    'Autre' => 0
                ];

                // 4. Si cabinet existe et a des charges, les traiter
                if ($cabinet && $cabinet->charges) {
                    $charges = is_string($cabinet->charges) ? json_decode($cabinet->charges, true) : $cabinet->charges;
                    
                    if (is_array($charges)) {
                        Log::info("Processing " . count($charges) . " charges");
                        
                        foreach ($charges as $charge) {
                            // Filtrer par date si nécessaire
                            $includeCharge = true;
                            if (isset($charge['created_at'])) {
                                try {
                                    $chargeDate = Carbon::parse($charge['created_at']);
                                    $includeCharge = $chargeDate->between($chargesStartDate, $chargesEndDate);
                                } catch (\Exception $e) {
                                    Log::warning("Invalid charge date: " . $charge['created_at']);
                                    $includeCharge = true; // Inclure si date invalide
                                }
                            }

                            if ($includeCharge) {
                                $type = isset($charge['type']) ? $charge['type'] : 'Autre';
                                $value = floatval($charge['value'] ?? 0);

                                if (isset($chargesSums[$type])) {
                                    $chargesSums[$type] += $value;
                                } else {
                                    $chargesSums['Autre'] += $value;
                                }

                                Log::info("Added charge: $type = $value");
                            }
                        }
                    }
                }

                $totalCharges = array_sum($chargesSums);
                Log::info("Total charges calculated: " . $totalCharges);

                $colorMap = [
                    'Salaires' => '#10b981',      
                    'Cabinet charges' => '#3b82f6', 
                    'Crédits' => '#f59e0b',       
                    'Autre' => '#ef4444'          
                ];

                $totalChargesForPercentage = array_sum($chargesSums);
                if ($totalChargesForPercentage > 0) {
                    foreach ($chargesSums as $type => $sum) {
                        if ($sum > 0) {
                            // Calculer pourcentage par rapport au total des charges (pas des paiements)
                            $percentage = ($sum / $totalChargesForPercentage) * 100;
                            $charges_labels[] = $type;
                            $charges_data[] = round($percentage, 2);
                            $charges_colors[] = $colorMap[$type];
                            $charges_hover_data[] = number_format($sum, 2) . ' DH';
                            
                            Log::info("Charge added to chart: $type = $percentage% ($sum DH)");
                        }
                    }
                }
            }

            Log::info("Final charges data: " . json_encode([
                'labels' => $charges_labels,
                'data' => $charges_data,
                'colors' => $charges_colors,
                'hover_data' => $charges_hover_data
            ]));

            // Get current period counts - Filtré par médecin
            if ($medecinId) {
                $count_rvs_filtered = Rendezvous::where('medecin_id', $medecinId)
                    ->whereBetween('appointment_date', [$currentStartDate, $currentEndDate])
                    ->count();

                $count_facts_filtered = Facture::where('medecin_id', $medecinId)
                    ->whereBetween('date', [$currentStartDate, $currentEndDate])
                    ->count();

                $count_pats_filtered = Patient::where('medecin_id', $medecinId)
                    ->whereBetween('created_at', [$currentStartDate, $currentEndDate])
                    ->count();

                $count_pais_filtered = Paiement::whereHas('facture', function($query) use ($medecinId) {
                    $query->where('medecin_id', $medecinId);
                })
                ->whereBetween('date_paiement', [$currentStartDate, $currentEndDate])
                ->count();

                // Get previous period counts for comparison - Filtré par médecin
                $count_rvs_previous = Rendezvous::where('medecin_id', $medecinId)
                    ->whereBetween('appointment_date', [$previousStartDate, $previousEndDate])
                    ->count();

                $count_facts_previous = Facture::where('medecin_id', $medecinId)
                    ->whereBetween('date', [$previousStartDate, $previousEndDate])
                    ->count();

                $count_pats_previous = Patient::where('medecin_id', $medecinId)
                    ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
                    ->count();

                $count_pais_previous = Paiement::whereHas('facture', function($query) use ($medecinId) {
                    $query->where('medecin_id', $medecinId);
                })
                ->whereBetween('date_paiement', [$previousStartDate, $previousEndDate])
                ->count();
            } else {
                $count_rvs_filtered = 0;
                $count_facts_filtered = 0;
                $count_pats_filtered = 0;
                $count_pais_filtered = 0;
                $count_rvs_previous = 0;
                $count_facts_previous = 0;
                $count_pats_previous = 0;
                $count_pais_previous = 0;
            }

            // Calculate percentage differences (current vs previous period)
            $rvs_diff_percent = $count_rvs_previous > 0 ? 
                (($count_rvs_filtered - $count_rvs_previous) / $count_rvs_previous) * 100 : 
                ($count_rvs_filtered > 0 ? 100 : 0);

            $facts_diff_percent = $count_facts_previous > 0 ? 
                (($count_facts_filtered - $count_facts_previous) / $count_facts_previous) * 100 : 
                ($count_facts_filtered > 0 ? 100 : 0);

            $pats_diff_percent = $count_pats_previous > 0 ? 
                (($count_pats_filtered - $count_pats_previous) / $count_pats_previous) * 100 : 
                ($count_pats_filtered > 0 ? 100 : 0);

            $pais_diff_percent = $count_pais_previous > 0 ? 
                (($count_pais_filtered - $count_pais_previous) / $count_pais_previous) * 100 : 
                ($count_pais_filtered > 0 ? 100 : 0);

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

            // NEW: Calculate payment line chart max value
            $payment_line_max_val = 0;
            if (!empty($payment_line_paye_data) || !empty($payment_line_attente_data) || !empty($payment_line_echoue_data)) {
                $payment_line_max_val = max(array_merge($payment_line_paye_data, $payment_line_attente_data, $payment_line_echoue_data));
            }
            $payment_line_chart_max_y = $payment_line_max_val > 0 ? $payment_line_max_val * 1.1 : 10;

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
                    'disponibilitesJS' => $disponibilitesJS,
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
                    // NEW: Payment line chart data
                    'payment_line_labels' => $payment_line_labels,
                    'payment_line_paye_data' => $payment_line_paye_data,
                    'payment_line_attente_data' => $payment_line_attente_data,
                    'payment_line_echoue_data' => $payment_line_echoue_data,
                    'payment_line_chart_max_y' => $payment_line_chart_max_y,
                    // NEW: Charges doughnut chart data
                    'charges_labels' => $charges_labels,
                    'charges_data' => $charges_data,
                    'charges_colors' => $charges_colors,
                    'charges_hover_data' => $charges_hover_data,
                    'total_paid_amount' => $total_paid_amount,
                ];

                Log::info('Returning JSON response with charges data: ' . json_encode([
                    'charges_labels' => $charges_labels,
                    'charges_data' => $charges_data
                ]));
                
                return response()->json($response_data);
            }

            Log::info('Returning regular view.');
            return view('secretaire.dashboard', compact(
                'count_rvs',
                'count_facts',
                'count_pats',
                'count_pais',
                'latest_rvs',
                'latest_facs',
                'latest_pats',
                'latest_pais',
                'medecins',
                'secretaires',
                'patients',
                'factures',
                'disponibilites',
                'existingAppointmentsJS',
                'disponibilitesJS',
                'count_rvs_filtered',
                'count_facts_filtered',
                'count_pats_filtered',
                'count_pais_filtered',
                'rvs_diff_percent',
                'facts_diff_percent',
                'pats_diff_percent',
                'pais_diff_percent',
                'rdv_chart_labels',
                'rdv_chart_confirmed_data',
                'rdv_chart_pending_data',
                'rdv_chart_max_y',
                'financial_chart_labels',
                'financial_chart_data',
                'financial_chart_max_y',
                'period',
                // NEW: Add the new chart data to the compact
                'payment_line_labels',
                'payment_line_paye_data',
                'payment_line_attente_data',
                'payment_line_echoue_data',
                'payment_line_chart_max_y',
                'charges_labels',
                'charges_data',
                'charges_colors',
                'charges_hover_data',
                'total_paid_amount'
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