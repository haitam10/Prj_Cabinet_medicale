<?php

namespace App\Http\Controllers;

use App\Models\Rendezvous;
use App\Models\Patient;
use App\Models\User;
use App\Models\Disponibilite;
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

        // Get disponibilites for the current doctor
        $disponibilites = collect();
        if ($user->role === 'medecin') {
            $disponibilites = Disponibilite::where('medecin_id', $user->id)
                ->where('date', '>=', now()->toDateString()) // Only future dates
                ->orderBy('date', 'asc')
                ->get();
        } elseif ($user->role === 'secretaire' && $user->medecin_id) {
            $disponibilites = Disponibilite::where('medecin_id', $user->medecin_id)
                ->where('date', '>=', now()->toDateString()) // Only future dates
                ->orderBy('date', 'asc')
                ->get();
        }

        // Prepare JavaScript data
        $disponibilitesJS = $disponibilites->map(function($disp) {
            return [
                'date' => $disp->date,
                'heure_entree' => substr($disp->heure_entree, 0, 5),
                'heure_sortie' => substr($disp->heure_sortie, 0, 5)
            ];
        })->toArray();

        $existingAppointmentsJS = $rendezvous->map(function($rdv) {
            return [
                'id' => $rdv->id,
                'date' => $rdv->appointment_date->format('Y-m-d'),
                'time' => substr($rdv->appointment_time, 0, 5),
                'status' => $rdv->status
            ];
        })->toArray();

        return view('secretaire.rendezvous', [
            'latest_rvs' => $rendezvous,
            'patients' => $patients,
            'medecins' => $medecins,
            'disponibilites' => $disponibilites,
            'disponibilitesJS' => $disponibilitesJS,
            'existingAppointmentsJS' => $existingAppointmentsJS,
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

            // Validation des données
            $validated = $request->validate([
                'patient_id' => 'required|integer|exists:patients,id',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required|date_format:H:i',
                'duration' => 'nullable|integer|min:30|max:30', // Force 30 minutes
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
                'appointment_time.required' => 'L\'heure du rendez-vous est obligatoire.',
                'appointment_time.date_format' => 'Format d\'heure invalide (HH:MM).',
                'status.required' => 'Le statut est obligatoire.',
                'status.in' => 'Statut invalide.',
                'appointment_type.required' => 'Le type de rendez-vous est obligatoire.',
                'appointment_type.in' => 'Type de rendez-vous invalide.',
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

            // Force duration to 30 minutes
            $duration = 30;

            // Formater l'heure correctement
            $appointmentTime = $validated['appointment_time'];
            if (strlen($appointmentTime) === 5) {
                $appointmentTime .= ':00'; // Ajouter les secondes
            }

            // Vérifier que la date sélectionnée correspond à une disponibilité du médecin
            if ($validated['status'] !== 'cancelled') {
                $disponibilite = Disponibilite::where('medecin_id', $medecinId)
                    ->where('date', $validated['appointment_date'])
                    ->first();

                if (!$disponibilite) {
                    return back()
                        ->withErrors(['appointment_date' => 'Aucune disponibilité définie pour cette date.'])
                        ->withInput();
                }

                // Vérifier que l'heure est dans les créneaux de disponibilité
                $timeSlotValid = $this->validateTimeSlot(
                    $validated['appointment_time'],
                    $disponibilite->heure_entree,
                    $disponibilite->heure_sortie
                );

                if (!$timeSlotValid) {
                    return back()
                        ->withErrors(['appointment_time' => 'L\'heure sélectionnée n\'est pas dans les créneaux de disponibilité.'])
                        ->withInput();
                }

                // Vérifier les conflits d'horaire
                $conflictCheck = $this->checkTimeSlotConflict(
                    $medecinId,
                    $validated['appointment_date'],
                    $validated['appointment_time']
                );

                if ($conflictCheck['hasConflict']) {
                    return back()
                        ->withErrors(['appointment_time' => $conflictCheck['message']])
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
                'appointment_time' => 'required|date_format:H:i',
                'duration' => 'nullable|integer|min:30|max:30', // Force 30 minutes
                'status' => 'required|string|in:pending,confirmed,completed,cancelled',
                'appointment_type' => 'required|string|in:consultation,follow_up,emergency,routine',
                'reason' => 'nullable|string|max:1000',
                'patient_notes' => 'nullable|string|max:1000',
                'doctor_notes' => 'nullable|string|max:1000',
                'cancellation_reason' => 'nullable|string|max:500',
                'feedback' => 'nullable|string|max:1000',
            ]);

            // Force duration to 30 minutes
            $duration = 30;

            // Format appointment time correctly
            $appointmentTime = $validated['appointment_time'];
            if (strlen($appointmentTime) === 5) {
                $appointmentTime .= ':00'; // Add seconds if not present
            }

            // Validate availability and time slot only if not cancelled
            if ($validated['status'] !== 'cancelled') {
                $disponibilite = Disponibilite::where('medecin_id', $rendezvous->medecin_id)
                    ->where('date', $validated['appointment_date'])
                    ->first();

                if (!$disponibilite) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => 'Aucune disponibilité définie pour cette date.'], 422);
                    }
                    return back()
                        ->withErrors(['appointment_date' => 'Aucune disponibilité définie pour cette date.'])
                        ->withInput();
                }

                // Validate time slot
                $timeSlotValid = $this->validateTimeSlot(
                    $validated['appointment_time'],
                    $disponibilite->heure_entree,
                    $disponibilite->heure_sortie
                );

                if (!$timeSlotValid) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => 'L\'heure sélectionnée n\'est pas dans les créneaux de disponibilité.'], 422);
                    }
                    return back()
                        ->withErrors(['appointment_time' => 'L\'heure sélectionnée n\'est pas dans les créneaux de disponibilité.'])
                        ->withInput();
                }

                // Check for conflicts (excluding current appointment)
                $conflictCheck = $this->checkTimeSlotConflict(
                    $rendezvous->medecin_id,
                    $validated['appointment_date'],
                    $validated['appointment_time'],
                    $rendezvous->id
                );

                if ($conflictCheck['hasConflict']) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => $conflictCheck['message']], 422);
                    }
                    return back()
                        ->withErrors(['appointment_time' => $conflictCheck['message']])
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
     * Store a new disponibilite
     */
    public function storeDisponibilite(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'heure_entree' => 'required|date_format:H:i',
                'heure_sortie' => 'required|date_format:H:i|after:heure_entree',
            ], [
                'date.required' => 'La date est obligatoire.',
                'date.after_or_equal' => 'La date ne peut pas être dans le passé.',
                'heure_entree.required' => 'L\'heure d\'entrée est obligatoire.',
                'heure_sortie.required' => 'L\'heure de sortie est obligatoire.',
                'heure_sortie.after' => 'L\'heure de sortie doit être après l\'heure d\'entrée.',
            ]);

            // Déterminer le médecin ID
            if ($user->role === 'medecin') {
                $medecinId = $user->id;
                $secretaireId = null;
            } elseif ($user->role === 'secretaire' && $user->medecin_id) {
                $medecinId = $user->medecin_id;
                $secretaireId = $user->id;
            } else {
                return back()->withErrors(['authorization' => 'Non autorisé à créer des disponibilités.']);
            }

            // Vérifier si une disponibilité existe déjà pour cette date
            $existingDisponibilite = Disponibilite::where('medecin_id', $medecinId)
                ->where('date', $validated['date'])
                ->first();

            if ($existingDisponibilite) {
                return back()->withErrors(['date' => 'Une disponibilité existe déjà pour cette date.']);
            }

            Disponibilite::create([
                'medecin_id' => $medecinId,
                'secretaire_id' => $secretaireId,
                'date' => $validated['date'],
                'heure_entree' => $validated['heure_entree'],
                'heure_sortie' => $validated['heure_sortie'],
            ]);

            return back()->with('success', 'Disponibilité ajoutée avec succès.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la disponibilité: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création de la disponibilité.');
        }
    }

    /**
     * Update disponibilite
     */
    public function updateDisponibilite(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $disponibilite = Disponibilite::findOrFail($id);

            // Check authorization
            if ($user->role === 'medecin' && $disponibilite->medecin_id !== $user->id) {
                throw new \Exception('Non autorisé à modifier cette disponibilité.');
            } elseif ($user->role === 'secretaire' && $disponibilite->medecin_id !== $user->medecin_id) {
                throw new \Exception('Non autorisé à modifier cette disponibilité.');
            }

            $validated = $request->validate([
                'date' => 'required|date',
                'heure_entree' => 'required|date_format:H:i',
                'heure_sortie' => 'required|date_format:H:i|after:heure_entree',
            ]);

            // Check if another disponibilite exists for this date (excluding current one)
            $existingDisponibilite = Disponibilite::where('medecin_id', $disponibilite->medecin_id)
                ->where('date', $validated['date'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingDisponibilite) {
                return back()->withErrors(['date' => 'Une autre disponibilité existe déjà pour cette date.']);
            }

            $disponibilite->update([
                'date' => $validated['date'],
                'heure_entree' => $validated['heure_entree'],
                'heure_sortie' => $validated['heure_sortie'],
            ]);

            return back()->with('success', 'Disponibilité mise à jour avec succès.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la disponibilité: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour de la disponibilité.');
        }
    }

    /**
     * Delete disponibilite
     */
    public function destroyDisponibilite(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $disponibilite = Disponibilite::findOrFail($id);

            // Check authorization
            if ($user->role === 'medecin' && $disponibilite->medecin_id !== $user->id) {
                throw new \Exception('Non autorisé à supprimer cette disponibilité.');
            } elseif ($user->role === 'secretaire' && $disponibilite->medecin_id !== $user->medecin_id) {
                throw new \Exception('Non autorisé à supprimer cette disponibilité.');
            }

            // Check if there are existing appointments for this availability
            $existingAppointments = Rendezvous::where('medecin_id', $disponibilite->medecin_id)
                ->whereDate('appointment_date', $disponibilite->date)
                ->where('status', '!=', 'cancelled')
                ->count();

            if ($existingAppointments > 0) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Impossible de supprimer cette disponibilité car des rendez-vous sont programmés.'], 422);
                }
                return back()->withErrors(['delete' => 'Impossible de supprimer cette disponibilité car des rendez-vous sont programmés.']);
            }

            $disponibilite->delete();
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Disponibilité supprimée avec succès.']);
            }
            return back()->with('success', 'Disponibilité supprimée avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Validate if a time slot is within availability hours
     */
    private function validateTimeSlot($appointmentTime, $heureEntree, $heureSortie)
    {
        $appointmentTimeObj = Carbon::createFromFormat('H:i', $appointmentTime);
        $entreeTimeObj = Carbon::createFromFormat('H:i', substr($heureEntree, 0, 5));
        $sortieTimeObj = Carbon::createFromFormat('H:i', substr($heureSortie, 0, 5));
        
        // Check if appointment time is within availability hours
        // Also check if there's enough time for a 30-minute appointment
        $appointmentEndTime = $appointmentTimeObj->copy()->addMinutes(30);
        
        return $appointmentTimeObj->greaterThanOrEqualTo($entreeTimeObj) && 
               $appointmentEndTime->lessThanOrEqualTo($sortieTimeObj);
    }

    /**
     * Check for time slot conflicts
     */
    private function checkTimeSlotConflict($medecinId, $date, $time, $excludeRendezvousId = null)
    {
        $query = Rendezvous::where('medecin_id', $medecinId)
            ->where('status', '!=', 'cancelled')
            ->whereDate('appointment_date', $date)
            ->where('appointment_time', 'like', $time . '%'); // Match HH:MM format

        if ($excludeRendezvousId) {
            $query->where('id', '!=', $excludeRendezvousId);
        }

        $existingAppointment = $query->first();

        if ($existingAppointment) {
            $patientName = $existingAppointment->patient 
                ? $existingAppointment->patient->nom . ' ' . $existingAppointment->patient->prenom
                : 'Patient inconnu';

            return [
                'hasConflict' => true,
                'message' => "Ce créneau horaire est déjà occupé par un rendez-vous avec {$patientName}."
            ];
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