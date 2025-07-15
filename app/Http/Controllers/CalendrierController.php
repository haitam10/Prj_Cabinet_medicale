<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rendezvous;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        // Définir la locale de Carbon en français pour cette requête
        Carbon::setLocale('fr');

        $medecinId = Auth::id(); // Récupère l'ID du médecin connecté

        if (!$medecinId) {
            // Gérer le cas où l'utilisateur n'est pas connecté ou n'est pas un médecin
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder au calendrier.');
        }

        $currentDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();

        // Déterminer le début et la fin de la semaine (lundi au dimanche)
        $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

        // Récupérer les rendez-vous pour le médecin connecté dans la semaine
        $appointments = Rendezvous::with('patient')
            ->where('medecin_id', $medecinId)
            ->whereBetween('date', [$startOfWeek->startOfDay(), $endOfWeek->endOfDay()])
            ->orderBy('date')
            ->get();

        // Organiser les rendez-vous par jour de la semaine
        $daysOfWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $daysOfWeek[] = [
                'date' => $date,
                'appointments' => $appointments->filter(function ($appointment) use ($date) {
                    return Carbon::parse($appointment->date)->isSameDay($date);
                })->values(), // Réinitialiser les clés après le filtrage
            ];
        }

        return view('secretaire.calendrier', compact('daysOfWeek', 'startOfWeek', 'endOfWeek', 'currentDate'));
    }
}
