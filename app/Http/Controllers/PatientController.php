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

        return view('secretaire.patients', compact('patients'));
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

        try {
            $patient = Patient::create($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Patient créé avec succès.',
                    'patient' => $patient
                ], 201);
            }

            return redirect()->back()->with('success', 'Patient créé avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création du patient.'], 500);
            }

            return redirect()->back()->with('error', 'Erreur lors de la création du patient.');
        }
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

        try {
            $patient->update($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Patient mis à jour avec succès.',
                    'patient' => $patient
                ]);
            }

            return redirect()->route('secretaire.patients')->with('success', 'Patient mis à jour avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la mise à jour du patient.'], 500);
            }

            return redirect()->route('secretaire.patients')->with('error', 'Erreur lors de la mise à jour du patient.');
        }
    }

    public function destroy(Request $request, Patient $patient)
    {
        try {
            $patient->delete();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Patient supprimé avec succès.']);
            }

            return redirect()->route('secretaire.patients')->with('success', 'Patient supprimé avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression du patient.'], 500);
            }

            return redirect()->route('secretaire.patients')->with('error', 'Erreur lors de la suppression du patient.');
        }
    }
}