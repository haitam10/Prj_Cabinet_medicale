<?php

namespace App\Http\Controllers;

use App\Models\Rendezvous;
use App\Models\User;
use App\Models\Facture;
use App\Models\Patient;
use App\Models\Paiement;

use Illuminate\Http\Request;

class SecretaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function Home(Request $request)
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



    /**
     * Show the form for creating a new resource.
    //  */
    // public function create(Request $request)
    // {
    //     if ($request->wantsJson()) {
    //         return response()->json(['message' => 'Formulaire non disponible via API'], 405);
    //     }

    //     return view('users.create');
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'cin' => 'required|string|unique:users,cin',
    //         'nom' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:6|confirmed',
    //         'role' => 'required|string',
    //         'statut' => 'required|string',
    //     ]);

    //     $user = User::create([
    //         'cin' => $validated['cin'],
    //         'nom' => $validated['nom'],
    //         'email' => $validated['email'],
    //         'password' => Hash::make($validated['password']),
    //         'role' => $validated['role'],
    //         'statut' => $validated['statut'],
    //     ]);

    //     if ($request->wantsJson()) {
    //         return response()->json(['message' => 'Utilisateur créé avec succès.', 'user' => $user], 201);
    //     }

    //     return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Request $request, string $id)
    // {
    //     $user = User::findOrFail($id);

    //     if ($request->wantsJson()) {
    //         return response()->json($user);
    //     }

    //     return view('users.show', compact('user'));
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(Request $request, string $id)
    // {
    //     $user = User::findOrFail($id);

    //     if ($request->wantsJson()) {
    //         return response()->json(['message' => 'Formulaire non disponible via API'], 405);
    //     }

    //     return view('users.edit', compact('user'));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     $user = User::findOrFail($id);

    //     $validated = $request->validate([
    //         'cin' => 'required|string|unique:users,cin,' . $user->id,
    //         'nom' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'role' => 'required|string',
    //         'statut' => 'required|string',
    //         'password' => 'nullable|string|min:6|confirmed',
    //     ]);

    //     $data = $request->only('cin', 'nom', 'email', 'role', 'statut');

    //     if ($request->filled('password')) {
    //         $data['password'] = Hash::make($request->password);
    //     }

    //     $user->update($data);

    //     if ($request->wantsJson()) {
    //         return response()->json(['message' => 'Utilisateur mis à jour avec succès.', 'user' => $user]);
    //     }

    //     return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Request $request, string $id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->delete();

    //     if ($request->wantsJson()) {
    //         return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    //     }

    //     return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    // }
}
