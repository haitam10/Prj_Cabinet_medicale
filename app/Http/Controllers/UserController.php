<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $users = User::paginate(10);

            if ($request->wantsJson()) {
                return response()->json($users);
            }

            return view('users.index', compact('users'));
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
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cin' => 'required|string|unique:users,cin',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
            'statut' => 'required|string',
            'date_naissance' => 'nullable|date|before:today',
            'sexe' => 'nullable|string|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'specialite' => 'nullable|string|max:255',
            'numero_adeli' => 'nullable|string|max:50',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Utilisateur créé avec succès.', 'user' => $user], 201);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($user);
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'cin' => 'required|string|unique:users,cin,' . $user->id,
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'statut' => 'required|string',
            'date_naissance' => 'nullable|date|before:today',
            'sexe' => 'nullable|string|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'specialite' => 'nullable|string|max:255',
            'numero_adeli' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Utilisateur mis à jour avec succès.', 'user' => $user]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Affiche le profil de l'utilisateur connecté.
     */
    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('secretaire.profile', compact('user'));
    }

    /**
     * Met à jour le profil de l'utilisateur connecté.
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'cin' => 'required|string|max:20|unique:users,cin,' . $user->id,
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'statut' => 'required|string|in:actif,inactif,suspendu',
            'date_naissance' => 'nullable|date|before:today',
            'sexe' => 'nullable|string|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'specialite' => 'nullable|string|max:255',
            'numero_adeli' => 'nullable|string|max:50',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->only([
            'cin', 'nom', 'email', 'statut', 'date_naissance',
            'sexe', 'telephone', 'adresse', 'specialite', 'numero_adeli'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('secretaire.profile')->with('success', 'Profil mis à jour avec succès !');
    }
}
