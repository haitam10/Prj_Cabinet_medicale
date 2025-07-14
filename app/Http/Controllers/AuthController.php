<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Rendezvous;
use App\Models\Facture;
use App\Models\Patient;
use App\Models\Paiement;

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
            return response()->json([
                'message' => 'Email ou mot de passe incorrect.'
            ], 401);
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
        'role' => 'required|in:admin,medecin,secretaire',
    ]);

    User::create([
        'cin' => $validated['cin'],
        'nom' => $validated['nom'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
        'statut' => 'inactif',
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
        'role' => 'required|in:admin,medecin,secretaire',
    ]);

    $user = User::create([
        'cin' => $validated['cin'],
        'nom' => $validated['nom'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
        'statut' => 'inactif',
    ]);

    return response()->json([
        'message' => 'Compte créé avec succès.',
        'user' => $user
    ], 201);
}


public function secretDash (Request $request)
{
    try {
        // Get the count of rows for each table
        $count_rvs = Rendezvous::count();
        $count_facts = Facture::count();
        $count_pats = Patient::count();
        $count_pais = Paiement::count();

        // Get the latest 5 records for each table
        $latest_rvs = Rendezvous::latest()->take(5)->get();
        $latest_facs = Facture::latest()->take(5)->get();
        $latest_pats = Patient::latest()->take(5)->get();
        $latest_pais = Paiement::latest()->take(5)->get();

        // // Paginate records if needed (optional, depending on the use case)
        $medecins = User::where('role', 'medecin')->get();

        // Fetching secretaires with pagination
        $secretaires = User::where('role', 'secretaire')->get();

        // Fetching all patients without pagination
        $patients = Patient::all();

        if ($request->wantsJson()) {
            // Return all the data as a JSON response
            return response()->json([
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
                'patients' => $patients
            ]);
        }

        // Return data to the view with counts and latest records
        return view('secretaire.dashboard', compact(
            'count_rvs', 'count_facts', 'count_pats', 'count_pais',
            'latest_rvs', 'latest_facs', 'latest_pats', 'latest_pais','medecins','secretaires','patients'
        ));
    } catch (\Throwable $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}



}