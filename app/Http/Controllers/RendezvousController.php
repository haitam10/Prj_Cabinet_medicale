<?php

namespace App\Http\Controllers;

use App\Models\Rendezvous;
use Illuminate\Http\Request;

class RendezvousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rendezvous = Rendezvous::paginate(10);

        if ($request->wantsJson()) {
            return response()->json($rendezvous);
        }

        return view('rendezvous.index', compact('rendezvous'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('rendezvous.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'secretaire_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'statut' => 'required|string',
            'motif' => 'nullable|string|max:255',
        ]);

        $rendezvous = Rendezvous::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Rendez-vous créé avec succès.',
                'rendezvous' => $rendezvous,
            ], 201);
        }

        return redirect()->route('rendezvous.index')->with('success', 'Rendez-vous créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Rendezvous $rendezvous)
    {
        if ($request->wantsJson()) {
            return response()->json($rendezvous);
        }

        return view('rendezvous.show', compact('rendezvous'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Rendezvous $rendezvous)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('rendezvous.edit', compact('rendezvous'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rendezvous $rendezvous)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'secretaire_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'statut' => 'required|string',
            'motif' => 'nullable|string|max:255',
        ]);

        $rendezvous->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Rendez-vous mis à jour avec succès.',
                'rendezvous' => $rendezvous,
            ]);
        }

        return redirect()->route('rendezvous.index')->with('success', 'Rendez-vous mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Rendezvous $rendezvous)
    {
        $rendezvous->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Rendez-vous supprimé avec succès.']);
        }

        return redirect()->route('rendezvous.index')->with('success', 'Rendez-vous supprimé avec succès.');
    }
}
