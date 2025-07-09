<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        ]);

        $user = User::create([
            'cin' => $validated['cin'],
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'statut' => $validated['statut'],
        ]);

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
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only('cin', 'nom', 'email', 'role', 'statut');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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
}
