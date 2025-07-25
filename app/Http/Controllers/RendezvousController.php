<?php

namespace App\Http\Controllers;

use App\Models\Rendezvous;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RendezvousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Rendezvous::with(['patient', 'medecin', 'secretaire']);

        // Filter appointments based on user role
        if ($user->role === 'medecin') {
            // Show doctor's own appointments
            $query->where('medecin_id', $user->id);
        } elseif ($user->role === 'secretaire') {
            // Show appointments of the doctor associated with this secretary
            if ($user->medecin_id) {
                $query->where('medecin_id', $user->medecin_id);
            } else {
                // If secretary has no associated doctor, show no appointments
                $query->where('id', null);
            }
        }

        // Search by patient name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhere('prenom', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by appointment type
        if ($request->has('appointment_type') && !empty($request->appointment_type)) {
            $query->where('appointment_type', $request->appointment_type);
        }

        // Filter by date
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('appointment_date', $request->date);
        }

        $rendezvous = $query->orderBy('appointment_date', 'desc')
                           ->orderBy('appointment_time', 'desc')
                           ->paginate(10);

        // Format data for JavaScript consumption
        $rendezvous->getCollection()->transform(function ($rdv) {
            $rdv->appointment_date_formatted = $rdv->appointment_date->format('Y-m-d');
            $rdv->appointment_time_formatted = substr($rdv->appointment_time, 0, 5);
            return $rdv;
        });

        if ($request->wantsJson()) {
            return response()->json($rendezvous);
        }

        $patients = Patient::all();
        
        // Get doctors based on user role
        if ($user->role === 'medecin') {
            $medecins = User::where('id', $user->id)->get();
        } elseif ($user->role === 'secretaire' && $user->medecin_id) {
            $medecins = User::where('id', $user->medecin_id)->get();
        } else {
            $medecins = collect();
        }

        return view('secretaire.rendezvous', [
            'latest_rvs' => $rendezvous,
            'patients' => $patients,
            'medecins' => $medecins,
            'search' => $request->search,
            'status' => $request->status,
            'appointment_type' => $request->appointment_type,
            'date' => $request->date,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            Log::info('Début création rendez-vous', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'request_data' => $request->all()
            ]);

            // Validation des données - CORRECTION ICI
            $validated = $request->validate([
                'patient_id' => 'required|integer|exists:patients,id',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required|date_format:H:i', // LIGNE CORRIGÉE
                'duration' => 'nullable|integer|min:15|max:180',
                'status' => 'required|string|in:pending,confirmed,completed,cancelled',
                'appointment_type' => 'required|string|in:consultation,follow_up,emergency,routine',
                'reason' => 'nullable|string|max:1000',
                'patient_notes' => 'nullable|string|max:1000',
                'doctor_notes' => 'nullable|string|max:1000',
                'cancellation_reason' => 'nullable|string|max:500',
                'feedback' => 'nullable|string|max:1000',
            ], [
                'patient_id.required' => 'Veuillez sélectionner un patient.',
                'patient_id.exists' => 'Le patient sélectionné n\'existe pas.',
                'appointment_date.required' => 'La date du rendez-vous est obligatoire.',
                'appointment_date.after_or_equal' => 'La date du rendez-vous ne peut pas être dans le passé.',
                'appointment_time.required' => 'L\'heure du rendez-vous est obligatoire.',
                'appointment_time.date_format' => 'Format d\'heure invalide (HH:MM).',
                'status.required' => 'Le statut est obligatoire.',
                'status.in' => 'Statut invalide.',
                'appointment_type.required' => 'Le type de rendez-vous est obligatoire.',
                'appointment_type.in' => 'Type de rendez-vous invalide.',
                'duration.integer' => 'La durée doit être un nombre entier.',
                'duration.min' => 'La durée minimale est de 15 minutes.',
                'duration.max' => 'La durée maximale est de 180 minutes.',
            ]);

            // Déterminer le médecin ID en fonction du rôle de l'utilisateur
            if ($user->role === 'medecin') {
                $medecinId = $user->id;
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $medecinId = $user->medecin_id;
            } else {
                return back()
                    ->withErrors(['authorization' => 'Utilisateur non autorisé à créer des rendez-vous.'])
                    ->withInput();
            }

            // Assurer que duration est un entier
            $duration = (int) ($validated['duration'] ?? 30);

            // Formater l'heure correctement
            $appointmentTime = $validated['appointment_time'];
            if (strlen($appointmentTime) === 5) {
                $appointmentTime .= ':00'; // Ajouter les secondes
            }

            // Créer la date/heure complète pour vérifier les conflits
            $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                $validated['appointment_date'] . ' ' . $appointmentTime);

            // Vérifier les conflits d'horaire seulement si non annulé
            if ($validated['status'] !== 'cancelled') {
                // Vérifier que la date/heure n'est pas dans le passé
                if ($appointmentDateTime->isPast()) {
                    return back()
                        ->withErrors(['datetime' => 'La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.'])
                        ->withInput();
                }

                $conflictCheck = $this->checkDoctorScheduleConflict($medecinId, $appointmentDateTime, $duration);
                
                if ($conflictCheck['hasConflict']) {
                    return back()
                        ->withErrors(['schedule' => $conflictCheck['message']])
                        ->withInput();
                }
            }

            // Préparer les données pour la création
            $createData = [
                'patient_id' => (int) $validated['patient_id'],
                'medecin_id' => (int) $medecinId,
                'secretaire_id' => $user->role === 'secretaire' ? $user->id : null,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $appointmentTime,
                'duration' => $duration,
                'status' => $validated['status'],
                'appointment_type' => $validated['appointment_type'],
                'reason' => $validated['reason'],
                'patient_notes' => $validated['patient_notes'],
                'doctor_notes' => $validated['doctor_notes'],
                'cancellation_reason' => $validated['cancellation_reason'],
                'feedback' => $validated['feedback'],
            ];

            // Ajouter cancelled_at si le statut est annulé
            if ($validated['status'] === 'cancelled') {
                $createData['cancelled_at'] = now();
            }

            Log::info('Données pour création de rendez-vous', $createData);

            // Créer le rendez-vous
            $rendezvous = Rendezvous::create($createData);

            Log::info('Rendez-vous créé avec succès', ['id' => $rendezvous->id]);

            return redirect()->route('secretaire.rendezvous')
                ->with('success', 'Rendez-vous créé avec succès.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation lors de la création du rendez-vous', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du rendez-vous', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return back()
                ->with('error', 'Erreur lors de la création du rendez-vous: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $rendezvous = Rendezvous::findOrFail($id);

            // Check if user can edit this appointment
            if ($user->role === 'medecin' && $rendezvous->medecin_id !== $user->id) {
                throw new \Exception('Non autorisé à modifier ce rendez-vous.');
            } elseif ($user->role === 'secretaire' && $rendezvous->medecin_id !== $user->medecin_id) {
                throw new \Exception('Non autorisé à modifier ce rendez-vous.');
            }

            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required|date_format:H:i', // CORRECTION ICI AUSSI
                'duration' => 'nullable|integer|min:15|max:180',
                'status' => 'required|string|in:pending,confirmed,completed,cancelled',
                'appointment_type' => 'required|string|in:consultation,follow_up,emergency,routine',
                'reason' => 'nullable|string|max:1000',
                'patient_notes' => 'nullable|string|max:1000',
                'doctor_notes' => 'nullable|string|max:1000',
                'cancellation_reason' => 'nullable|string|max:500',
                'feedback' => 'nullable|string|max:1000',
            ]);

            // Ensure duration is an integer
            $duration = (int) ($validated['duration'] ?? 30);

            // Format appointment time correctly
            $appointmentTime = $validated['appointment_time'];
            if (strlen($appointmentTime) === 5) {
                $appointmentTime .= ':00'; // Add seconds if not present
            }

            // Create appointment datetime for conflict checking
            $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                $validated['appointment_date'] . ' ' . $appointmentTime);

            // Only check past datetime if not cancelled
            if ($validated['status'] !== 'cancelled' && $appointmentDateTime->isPast()) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.'], 422);
                }
                return back()
                    ->withErrors(['datetime' => 'La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.'])
                    ->withInput();
            }
            
            // Check for conflicts (excluding current appointment) only if not cancelled
            if ($validated['status'] !== 'cancelled') {
                $conflictCheck = $this->checkDoctorScheduleConflict(
                    $rendezvous->medecin_id,
                    $appointmentDateTime,
                    $duration,
                    $rendezvous->id
                );
                
                if ($conflictCheck['hasConflict']) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => $conflictCheck['message']], 422);
                    }
                    return back()
                        ->withErrors(['schedule' => $conflictCheck['message']])
                        ->withInput();
                }
            }

            // Update cancellation timestamp if status changed to cancelled
            $updateData = [
                'patient_id' => $validated['patient_id'],
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $appointmentTime,
                'duration' => $duration,
                'status' => $validated['status'],
                'appointment_type' => $validated['appointment_type'],
                'reason' => $validated['reason'],
                'patient_notes' => $validated['patient_notes'],
                'doctor_notes' => $validated['doctor_notes'],
                'cancellation_reason' => $validated['cancellation_reason'],
                'feedback' => $validated['feedback'],
            ];

            if ($validated['status'] === 'cancelled' && $rendezvous->status !== 'cancelled') {
                $updateData['cancelled_at'] = now();
            } elseif ($validated['status'] !== 'cancelled') {
                $updateData['cancelled_at'] = null;
            }

            $rendezvous->update($updateData);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Rendez-vous mis à jour avec succès.',
                    'rendezvous' => $rendezvous->fresh(['patient', 'medecin']),
                ]);
            }

            return back()->with('success', 'Rendez-vous mis à jour avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du rendez-vous: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la mise à jour.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $rendezvous = Rendezvous::findOrFail($id);

            // Check if user can delete this appointment
            if ($user->role === 'medecin' && $rendezvous->medecin_id !== $user->id) {
                throw new \Exception('Non autorisé à supprimer ce rendez-vous.');
            } elseif ($user->role === 'secretaire' && $rendezvous->medecin_id !== $user->medecin_id) {
                throw new \Exception('Non autorisé à supprimer ce rendez-vous.');
            }

            $rendezvous->delete();
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Rendez-vous supprimé avec succès.']);
            }
            return back()->with('success', 'Rendez-vous supprimé avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Check for doctor schedule conflicts
     */
    private function checkDoctorScheduleConflict($medecinId, $appointmentDateTime, $duration, $excludeRendezvousId = null)
    {
        // Ensure duration is an integer
        $duration = (int) $duration;
        
        $appointmentEnd = $appointmentDateTime->copy()->addMinutes($duration);
        $bufferTime = 10; // 10 minutes buffer between appointments

        // Look for existing appointments that might conflict
        $query = Rendezvous::where('medecin_id', $medecinId)
            ->where('status', '!=', 'cancelled')
            ->whereDate('appointment_date', $appointmentDateTime->toDateString());

        if ($excludeRendezvousId) {
            $query->where('id', '!=', $excludeRendezvousId);
        }

        $existingAppointments = $query->get();

        foreach ($existingAppointments as $existing) {
            // Parse the time properly - appointment_time is a string in HH:MM:SS format
            $timeString = (string) $existing->appointment_time;
            if (strlen($timeString) > 5) {
                $timeString = substr($timeString, 0, 5); // Get HH:MM format
            }
            
            $existingStart = Carbon::createFromFormat('Y-m-d H:i', 
                $existing->appointment_date->format('Y-m-d') . ' ' . $timeString);
            
            // Ensure existing duration is an integer
            $existingDuration = (int) $existing->duration;
            $existingEnd = $existingStart->copy()->addMinutes($existingDuration);

            // Check if appointments overlap (with buffer)
            $proposedStart = $appointmentDateTime->copy()->subMinutes($bufferTime);
            $proposedEnd = $appointmentEnd->copy()->addMinutes($bufferTime);

            if ($proposedStart->lt($existingEnd) && $proposedEnd->gt($existingStart)) {
                $conflictTime = $existingStart->format('H:i');
                $conflictDate = $existingStart->format('d/m/Y');
                $patientName = $existing->patient 
                    ? $existing->patient->nom . ' ' . $existing->patient->prenom
                    : 'Patient inconnu';

                return [
                    'hasConflict' => true,
                    'message' => "Conflit d'horaire détecté ! Le médecin a déjà un rendez-vous le {$conflictDate} à {$conflictTime} avec {$patientName}. Durée: {$existingDuration} minutes."
                ];
            }
        }

        return ['hasConflict' => false];
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
        $user = Auth::user();
        
        if ($user->role === 'medecin') {
            $medecins = User::where('id', $user->id)->get();
        } elseif ($user->role === 'secretaire' && $user->medecin_id) {
            $medecins = User::where('id', $user->medecin_id)->get();
        } else {
            $medecins = collect();
        }
        
        return view('rendezvous.create', compact('patients', 'medecins'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Rendezvous $rendezvous)
    {
        $user = Auth::user();
        
        // Check authorization
        if ($user->role === 'medecin' && $rendezvous->medecin_id !== $user->id) {
            abort(403, 'Non autorisé à voir ce rendez-vous.');
        } elseif ($user->role === 'secretaire' && $rendezvous->medecin_id !== $user->medecin_id) {
            abort(403, 'Non autorisé à voir ce rendez-vous.');
        }

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
        
        $user = Auth::user();
        
        // Check authorization
        if ($user->role === 'medecin' && $rendezvous->medecin_id !== $user->id) {
            abort(403, 'Non autorisé à modifier ce rendez-vous.');
        } elseif ($user->role === 'secretaire' && $rendezvous->medecin_id !== $user->medecin_id) {
            abort(403, 'Non autorisé à modifier ce rendez-vous.');
        }

        $patients = Patient::all();
        
        if ($user->role === 'medecin') {
            $medecins = User::where('id', $user->id)->get();
        } elseif ($user->role === 'secretaire' && $user->medecin_id) {
            $medecins = User::where('id', $user->medecin_id)->get();
        } else {
            $medecins = collect();
        }
        
        return view('rendezvous.edit', compact('rendezvous', 'patients', 'medecins'));
    }
}