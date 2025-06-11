<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlotOccurence;
use App\Models\Slot;
use App\Models\Club;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Affiche la vue calendrier pour le mois demandé.
     * Si aucun mois n'est fourni, le mois courant est utilisé.
     */
    public function index(Request $request)
    {
        // Si un mois est fourni via GET, on le stocke en session
        if ($request->has('month')) {
            session(['calendar_month' => $request->input('month')]);
        }

        // Sinon on vérifie si un mois est en session
        $month = session('calendar_month', Carbon::now()->format('Y-m'));

        

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

        $club_id = auth()->user()->club_id;
        $club = Club::find($club_id);

        $slots = Slot::where('club_id', $club_id)->get();
        $nextSlot = SlotOccurence::with('slot')
            ->whereIn('slot_id', $slots->pluck('id'))
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where(function($q) {
                $q->whereHas('attendees', fn($q) => $q->where('user_id', auth()->id()))
                ->orWhereHas('monitors', fn($q) => $q->where('user_id', auth()->id()));
            })
            ->orderBy('date', 'asc')
            ->orderBy('slot_id', 'asc')
            ->first();
        

        return view('calendar_view', compact(
            'calendar_days', 'current_month_name', 'current_year', 'prev_month', 'next_month', 'current_month', 'nextSlot'
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


    public function getSlotOccurenceDetail($id)
    {
        $slotOccurence = SlotOccurence::with(['slot', 'attendees.user', 'monitors.user', 'cancellation'])->findOrFail($id);

        // Prétraitements nécessaires (même logique que dans la vue standard)
        $isRegisteredAsMember = $slotOccurence->attendees()->where('user_id', auth()->id())->exists();
        $isRegisteredAsMonitor = $slotOccurence->monitors()->where('user_id', auth()->id())->exists();

        $courseDateTime = Carbon::parse($slotOccurence->date . ' ' . $slotOccurence->slot->start_time);
        $registrationClosed = false;

        if ($slotOccurence->slot->auto_close && !is_null($slotOccurence->slot->close_duration)) {
            $deadline = Carbon::now()->addHours($slotOccurence->slot->close_duration);
            if ($deadline->greaterThan($courseDateTime)) {
                $registrationClosed = true;
            }
        }

        // Retour de la partial "slot_tile"
        return view('AdminClub.partials.slot_tile', compact(
            'slotOccurence',
            'isRegisteredAsMember',
            'isRegisteredAsMonitor',
            'registrationClosed'
        ));
    }
}
