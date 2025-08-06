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
        Carbon::setLocale('fr');

        $medecinId = Auth::id(); 

        if (!$medecinId) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder au calendrier.');
        }

        $currentDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $view = $request->input('view', 'week'); // Par défaut: vue semaine

        // Définir les dates selon la vue
        switch ($view) {
            case 'day':
                $startDate = $currentDate->copy()->startOfDay();
                $endDate = $currentDate->copy()->endOfDay();
                break;
            case 'month':
                $startDate = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
                $endDate = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
                break;
            default: // week
                $startDate = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
                $endDate = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);
                break;
        }

        $appointments = Rendezvous::with('patient')
            ->where('medecin_id', $medecinId)
            ->whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Préparer les données selon la vue
        $calendarData = [];
        
        if ($view === 'day') {
            // Vue jour - un seul jour
            $calendarData[] = [
                'date' => $currentDate,
                'appointments' => $appointments->filter(function ($appointment) use ($currentDate) {
                    return Carbon::parse($appointment->appointment_date)->format('Y-m-d') === $currentDate->format('Y-m-d');
                })->values(),
            ];
        } elseif ($view === 'month') {
            // Vue mois - tous les jours du mois avec padding
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $calendarData[] = [
                    'date' => $current->copy(),
                    'appointments' => $appointments->filter(function ($appointment) use ($current) {
                        return Carbon::parse($appointment->appointment_date)->format('Y-m-d') === $current->format('Y-m-d');
                    })->values(),
                ];
                $current->addDay();
            }
        } else {
            // Vue semaine - 7 jours
            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i);
                $calendarData[] = [
                    'date' => $date,
                    'appointments' => $appointments->filter(function ($appointment) use ($date) {
                        return Carbon::parse($appointment->appointment_date)->format('Y-m-d') === $date->format('Y-m-d');
                    })->values(),
                ];
            }
        }

        // Calculer les dates de navigation
        $prevDate = $this->getPreviousDate($currentDate, $view);
        $nextDate = $this->getNextDate($currentDate, $view);

        // Titre selon la vue
        $title = $this->getTitle($currentDate, $view, $startDate, $endDate);

        return view('secretaire.calendrier', compact(
            'calendarData', 
            'currentDate', 
            'view', 
            'prevDate', 
            'nextDate', 
            'title',
            'startDate',
            'endDate'
        ));
    }

    private function getPreviousDate($currentDate, $view)
    {
        switch ($view) {
            case 'day':
                return $currentDate->copy()->subDay();
            case 'month':
                return $currentDate->copy()->subMonth();
            default: // week
                return $currentDate->copy()->subWeek();
        }
    }

    private function getNextDate($currentDate, $view)
    {
        switch ($view) {
            case 'day':
                return $currentDate->copy()->addDay();
            case 'month':
                return $currentDate->copy()->addMonth();
            default: // week
                return $currentDate->copy()->addWeek();
        }
    }

    private function getTitle($currentDate, $view, $startDate, $endDate)
    {
        switch ($view) {
            case 'day':
                return $currentDate->isoFormat('dddd D MMMM YYYY');
            case 'month':
                return $currentDate->isoFormat('MMMM YYYY');
            default: // week
                return 'Semaine du ' . $startDate->isoFormat('D MMMM YYYY') . ' au ' . $endDate->isoFormat('D MMMM YYYY');
        }
    }
}