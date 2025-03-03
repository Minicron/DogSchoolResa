<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlotOccurence;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Affiche la vue calendrier pour le mois demandé.
     * Si aucun mois n'est fourni, le mois courant est utilisé.
     */
    public function index(Request $request)
    {
        // Récupérer le mois demandé au format "YYYY-MM" ou utiliser le mois courant
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();

        // Récupérer toutes les occurrences programmées durant ce mois
        $slotOccurences = SlotOccurence::with('slot')
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('date', 'asc')
            ->get();

        // Construire la grille du calendrier :
        // Début : le début de la semaine contenant le premier jour du mois (supposé début de semaine lundi)
        $calendar_days = [];
        $current = $startOfMonth->copy()->startOfWeek();
        $endCalendar = $endOfMonth->copy()->endOfWeek();
        while ($current->lte($endCalendar)) {
            // Filtrer les événements se produisant ce jour
            $events = $slotOccurences->filter(function ($occurrence) use ($current) {
                return Carbon::parse($occurrence->date)->isSameDay($current);
            })->values();
            $calendar_days[] = [
                'date'   => $current->format('Y-m-d'),
                'number' => $current->day,
                'events' => $events,
            ];
            $current->addDay();
        }

        // Préparer les variables de navigation
        $current_month_name = $startOfMonth->locale('fr')->isoFormat('MMMM');
        $current_year       = $startOfMonth->year;
        $prev_month         = $startOfMonth->copy()->subMonth()->format('Y-m');
        $next_month         = $startOfMonth->copy()->addMonth()->format('Y-m');
        $current_month      = $startOfMonth->month;
        

        return view('calendar_view', compact(
            'calendar_days', 'current_month_name', 'current_year', 'prev_month', 'next_month', 'current_month'
        ));
    }

    /**
     * Renvoie les détails d'un créneau pour affichage dans une popup.
     */
    public function eventDetails($id)
    {
        $event = SlotOccurence::with('slot')->findOrFail($id);
        return view('partials.calendar_event_details', compact('event'));
    }
}
