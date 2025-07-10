<?php

namespace App\Http\Controllers;

use App\Models\Rendezvous;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RendezvousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rendezvous = Rendezvous::with(['patient', 'medecin'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($rendezvous);
        }

        // Récupérer les patients et médecins pour les formulaires
        $patients = Patient::all();
        $medecins = User::where('role', 'medecin')->get();

        return view('secretaire.rendezvous', [
            'latest_rvs' => $rendezvous,
            'patients' => $patients,
            'medecins' => $medecins
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulaire non disponible via API'], 405);
        }

        $patients = Patient::all();
        $medecins = User::where('role', 'medecin')->get();
        
        return view('rendezvous.create', compact('patients', 'medecins'));
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
            'heure' => 'required|date_format:H:i',
            'statut' => 'required|string|in:confirmé,en attente,annulé',
            'motif' => 'nullable|string|max:255',
        ]);

        $datetime = $validated['date'] . ' ' . $validated['heure'] . ':00';
        
        // Validation : la date/heure ne peut pas être dans le passé
        $appointmentDateTime = Carbon::parse($datetime);
        if ($appointmentDateTime->isPast()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.'
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors(['datetime' => 'La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.'])
                ->withInput();
        }

        $rendezvous = Rendezvous::create([
            'patient_id' => $validated['patient_id'],
            'medecin_id' => $validated['medecin_id'],
            'secretaire_id' => $validated['secretaire_id'] ?? null,
            'date' => $datetime,
            'statut' => $validated['statut'],
            'motif' => $validated['motif'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Rendez-vous créé avec succès.',
                'rendezvous' => $rendezvous,
            ], 201);
        }

        return redirect()->route('secretaire.rendezvous')->with('success', 'Rendez-vous créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Rendezvous $rendezvous)
    {
        $rendezvous->load(['patient', 'medecin', 'secretaire']);
        
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

        $patients = Patient::all();
        $medecins = User::where('role', 'medecin')->get();
        
        return view('rendezvous.edit', compact('rendezvous', 'patients', 'medecins'));
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
            'heure' => 'required|date_format:H:i',
            'statut' => 'required|string|in:confirmé,en attente,annulé',
            'motif' => 'nullable|string|max:255',
        ]);

        $datetime = $validated['date'] . ' ' . $validated['heure'] . ':00';
        
        // Validation : la date/heure ne peut pas être dans le passé
        $appointmentDateTime = Carbon::parse($datetime);
        if ($appointmentDateTime->isPast()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.'
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors(['datetime' => 'La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.'])
                ->withInput();
        }

        $rendezvous->update([
            'patient_id' => $validated['patient_id'],
            'medecin_id' => $validated['medecin_id'],
            'secretaire_id' => $validated['secretaire_id'] ?? null,
            'date' => $datetime,
            'statut' => $validated['statut'],
            'motif' => $validated['motif'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Rendez-vous mis à jour avec succès.',
                'rendezvous' => $rendezvous->fresh(['patient', 'medecin']),
            ]);
        }

        return redirect()
            ->route('secretaire.rendezvous')
            ->with('success', 'Rendez-vous mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Rendezvous $rendezvous)
    {
        try {
            $rendezvous->delete();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Rendez-vous supprimé avec succès.']);
            }

            return redirect()->route('secretaire.rendezvous')->with('success', 'Rendez-vous supprimé avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression.'], 500);
            }

            return redirect()->route('secretaire.rendezvous')->with('error', 'Erreur lors de la suppression.');
        }
    }
}