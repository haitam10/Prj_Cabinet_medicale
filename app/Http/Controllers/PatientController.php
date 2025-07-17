<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        Log::info('Tentative de création de patient', $request->all());

        $rules = [
            'cin' => 'required|string|unique:patients,cin',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:homme,femme',
            'date_naissance' => 'required|date|before_or_equal:today',
            'contact' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'telephone_secondaire' => 'nullable|string|max:255',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'antecedents' => 'nullable|string|max:1000',
            'medicaments' => 'nullable|string|max:1000',
            'poids' => 'nullable|numeric|min:0|max:999.99',
            'taille' => 'nullable|numeric|min:0|max:999.99',
            'profession' => 'nullable|string|max:255',
            'situation_familiale' => 'nullable|in:celibataire,marie,divorce,veuf',
        ];

        $messages = [
            'cin.unique' => 'Le patient existe déjà ! Un patient avec ce CIN est déjà enregistré dans le système.',
            'cin.required' => 'Le CIN est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être homme ou femme.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'contact.required' => 'Le téléphone principal est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'poids.numeric' => 'Le poids doit être un nombre.',
            'taille.numeric' => 'La taille doit être un nombre.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            $patient = Patient::create($validated);
            Log::info('Patient créé avec succès', ['patient_id' => $patient->id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Patient créé avec succès.',
                    'patient' => $patient
                ], 201);
            }
            return redirect()->route('secretaire.patients')->with('success', 'Patient ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du patient', ['error' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la création du patient.'], 500);
            }
            return redirect()->route('secretaire.patients')->with('error', 'Erreur lors de la création du patient : ' . $e->getMessage());
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
        $rules = [
            'cin' => 'required|string|unique:patients,cin,' . $patient->id,
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:homme,femme',
            'date_naissance' => 'required|date|before_or_equal:today',
            'contact' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'telephone_secondaire' => 'nullable|string|max:255',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'antecedents' => 'nullable|string|max:1000',
            'medicaments' => 'nullable|string|max:1000',
            'poids' => 'nullable|numeric|min:0|max:999.99',
            'taille' => 'nullable|numeric|min:0|max:999.99',
            'profession' => 'nullable|string|max:255',
            'situation_familiale' => 'nullable|in:celibataire,marie,divorce,veuf',
        ];

        $messages = [
            'cin.unique' => 'Le patient existe déjà ! Un patient avec ce CIN est déjà enregistré dans le système.',
            'cin.required' => 'Le CIN est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être homme ou femme.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'contact.required' => 'Le téléphone principal est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'poids.numeric' => 'Le poids doit être un nombre.',
            'taille.numeric' => 'La taille doit être un nombre.',
        ];

        $validated = $request->validate($rules, $messages);

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
            return redirect()->route('secretaire.patients')->with('error', 'Erreur lors de la mise à jour du patient : ' . $e->getMessage());
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
