<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::paginate(10);

        if ($request->wantsJson()) {
            return response()->json($patients);
        }

        return view('patients.index', compact('patients'));
    }

    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cin' => 'required|string|unique:patients,cin',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:homme,femme',
            'date_naissance' => 'required|date',
            'contact' => 'nullable|string|max:255',
        ]);

        $patient = Patient::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Patient créé avec succès.',
                'patient' => $patient
            ], 201);
        }

        return redirect()->route('patients.index')->with('success', 'Patient créé avec succès.');
    }

    public function show(Request $request, Patient $patient)
    {
        if ($request->wantsJson()) {
            return response()->json($patient);
        }

        return view('patients.show', compact('patient'));
    }

    public function edit(Request $request, Patient $patient)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'cin' => 'required|string|unique:patients,cin,' . $patient->id,
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:homme,femme',
            'date_naissance' => 'required|date',
            'contact' => 'nullable|string|max:255',
        ]);

        $patient->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Patient mis à jour avec succès.',
                'patient' => $patient
            ]);
        }

        return redirect()->route('patients.index')->with('success', 'Patient mis à jour avec succès.');
    }

    public function destroy(Request $request, Patient $patient)
    {
        $patient->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Patient supprimé avec succès.']);
        }

        return redirect()->route('patients.index')->with('success', 'Patient supprimé avec succès.');
    }
}
