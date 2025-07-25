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

        $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

        $appointments = Rendezvous::with('patient')
            ->where('medecin_id', $medecinId)
            ->whereBetween('appointment_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $daysOfWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $daysOfWeek[] = [
                'date' => $date,
                'appointments' => $appointments->filter(function ($appointment) use ($date) {
                    return Carbon::parse($appointment->appointment_date)->format('Y-m-d') === $date->format('Y-m-d');
                })->values(), 
            ];
        }

        return view('secretaire.calendrier', compact('daysOfWeek', 'startOfWeek', 'endOfWeek', 'currentDate'));
    }
}