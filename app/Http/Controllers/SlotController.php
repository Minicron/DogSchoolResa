<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Slot;
use App\Models\SlotOccurence;
use App\Models\SlotOccurenceAttendee;
use App\Models\SlotGroup;
use App\Notifications\SlotScheduleChangeNotification;


class SlotController extends Controller
{
    public function index()
    {
        return view('slot.index');
    }

    public function new()
    {
        if (auth()->user()->role != 'admin-club') {
            return redirect()->route('home');
        }

        if (request()->isMethod('post')) {
            $slot = new Slot();
            $slot->club_id = auth()->user()->club->id;
            $slot->name = request()->name;
            $slot->description = request()->description;
            $slot->location = request()->location;
            $slot->day_of_week = request()->day_of_week;
            // Les inputs du formulaire portent les noms "time_start" et "time_end"
            $slot->start_time = request()->time_start;
            $slot->end_time = request()->time_end;
            $slot->capacity_type = request()->capacity_type;
            
            // Gérer la capacité selon le type
            if (request()->capacity_type === 'fixed') {
                $slot->capacity = request()->capacity;
            } else { // none ou dynamic
                $slot->capacity = null;
            }
            $slot->auto_close = request()->has('auto_close');
            $slot->close_duration = $slot->auto_close ? request()->close_duration : null;
            $slot->is_restricted = request()->has('is_restricted');
            $slot->has_groups = request()->has('has_groups');
            $slot->save();

            // Debug: Vérifier les données sauvegardées
            Log::info('Données sauvegardées pour le slot', [
                'slot_id' => $slot->id,
                'auto_close' => $slot->auto_close,
                'close_duration' => $slot->close_duration,
                'is_restricted' => $slot->is_restricted,
            ]);

            // Supprimer les anciens groupes
            $slot->slotGroups()->delete();

            // Si des groupes ont été définis
            if ($slot->has_groups && request()->has('groups')) {
                foreach (request()->input('groups') as $index => $groupName) {
                    if (!empty($groupName)) {
                        SlotGroup::create([
                            'slot_id' => $slot->id,
                            'name'    => $groupName,
                            'order'   => $index,
                        ]);
                    }
                }
            }

            $slots = Slot::all();
            return view('AdminClub.slots', ['slots' => $slots]);
        }

        return view('Slot.new');
    }

    /**
     * Delete a slot
     * This will also delete the slot's occurences and attendees
     */
    public function delete(int $id)
    {
        if (auth()->user()->role != 'admin-club') {
            return redirect()->route('home');
        }

        $slot = Slot::find($id);

        // Manage slot's occurences
        $slotOccurences = SlotOccurence::where('slot_id', $slot->id)->get();
        foreach ($slotOccurences as $slotOccurence) {
            // Delete all attendees first
            $slotOccurenceAttendees = SlotOccurenceAttendee::where('slot_occurence_id', $slotOccurence->id)->get();
            foreach ($slotOccurenceAttendees as $slotOccurenceAttendee) {
                $slotOccurenceAttendee->delete();
            }
            // Then delete the occurence
            $slotOccurence->delete();
        }

        // Manage slot's attendees

        $slot->delete();

        $slots = Slot::all();

        return view('AdminClub.slots', ['slots' => $slots]);
    }

    /**
     * Edit a slot
     */
    public function edit(int $id)
    {
        if (auth()->user()->role != 'admin-club') {
            return redirect()->route('home');
        }

        $slot = Slot::find($id);

        if (request()->isMethod('post')) {

            // Debug: Afficher les données reçues
            Log::info('Données reçues pour modification du slot', [
                'slot_id' => $slot->id,
                'auto_close' => request()->has('auto_close'),
                'close_duration' => request()->input('close_duration'),
                'is_restricted' => request()->has('is_restricted'),
                'all_data' => request()->all()
            ]);

            // Vérifier si l'horaire a changé
            $dayChanged = $slot->day_of_week != request()->day_of_week;
            $timeChanged = $slot->start_time != request()->time_start || $slot->end_time != request()->time_end;
            
            if ($dayChanged || $timeChanged) {
                // Retourner une réponse JSON pour déclencher le popup de confirmation
                return response()->json([
                    'needs_confirmation' => true,
                    'message' => 'La modification de l\'horaire du créneau va annuler tous les cours futurs et notifier les participants. Voulez-vous continuer ?',
                    'old_schedule' => [
                        'day' => $slot->day_of_week,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time
                    ],
                    'new_schedule' => [
                        'day' => request()->day_of_week,
                        'start_time' => request()->time_start,
                        'end_time' => request()->time_end
                    ]
                ]);
            }

            // Si pas de changement d'horaire, procéder normalement
            $this->updateSlotWithoutScheduleChange($slot, request());

            return response()->json([
                'success' => true,
                'message' => 'Le créneau a été modifié avec succès.'
            ]);
        }

        return view('Slot.edit', [
            'slot' => $slot, 
            'groups' => $slot->slotGroups()->orderBy('order')->get()->pluck('name')->toArray()
        ]);
    }

    /**
     * Mettre à jour un slot sans changement d'horaire
     */
    private function updateSlotWithoutScheduleChange($slot, $request)
    {
        $slot->has_groups = $request->has('has_groups');
        $slot->save();

        // Supprimer les anciens groupes
        $slot->slotGroups()->delete();

        // Ajouter les nouveaux groupes si activés
        if ($slot->has_groups && $request->has('groups')) {
            foreach ($request->input('groups') as $index => $name) {
                if (trim($name) !== '') {
                    $slot->slotGroups()->create([
                        'name' => $name,
                        'order' => $index,
                    ]);
                }
            }
        }

        // Update the slot
        $slot->name = $request->name;
        $slot->description = $request->description;
        $slot->location = $request->location;
        $slot->day_of_week = $request->day_of_week;
        $slot->start_time = $request->time_start;
        $slot->end_time = $request->time_end;
        
        // Gérer la capacité selon le type
        $slot->capacity_type = $request->capacity_type;
        if ($request->capacity_type === 'fixed') {
            $slot->capacity = $request->capacity;
        } else { // none ou dynamic
            $slot->capacity = null;
        }
        
        // Gérer la clôture automatique des inscriptions
        $slot->auto_close = $request->has('auto_close');
        if ($request->has('auto_close') && $request->has('close_duration')) {
            $slot->close_duration = $request->close_duration;
        } else {
            $slot->close_duration = null;
        }
        
        // Gérer les restrictions d'inscription
        $slot->is_restricted = $request->has('is_restricted');
        
        $slot->save();
    }

    /**
     * Mettre à jour un slot avec changement d'horaire (procédure complexe)
     */
    public function updateSlotWithScheduleChange(int $id)
    {
        if (auth()->user()->role != 'admin-club') {
            return redirect()->route('home');
        }

        $slot = Slot::findOrFail($id);
        $request = request();

        // Debug: Log des données reçues
        Log::info('Données reçues pour modification avec changement d\'horaire', [
            'slot_id' => $id,
            'all_data' => $request->all(),
            'day_of_week' => $request->input('day_of_week'),
            'time_start' => $request->input('time_start'),
            'time_end' => $request->input('time_end'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'location' => $request->input('location')
        ]);

        // Sauvegarder les anciennes données pour les notifications
        $oldSchedule = [
            'day' => $slot->day_of_week,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time
        ];

        // Créer le nouveau planning pour les notifications
        $newSchedule = [
            'day' => $request->input('day_of_week'),
            'start_time' => $request->input('time_start'),
            'end_time' => $request->input('time_end')
        ];

        // Récupérer toutes les occurrences futures
        $futureOccurrences = $slot->occurrences()
            ->where('date', '>', now())
            ->where('is_cancelled', false)
            ->with(['attendees.user', 'monitors.user'])
            ->get();

        // Notifier tous les participants des occurrences futures
        foreach ($futureOccurrences as $occurrence) {
            // Notifier les participants
            foreach ($occurrence->attendees as $attendee) {
                $attendee->user->notify(new SlotScheduleChangeNotification($slot, $occurrence, $oldSchedule, $newSchedule));
            }
            
            // Notifier les moniteurs
            foreach ($occurrence->monitors as $monitor) {
                $monitor->user->notify(new SlotScheduleChangeNotification($slot, $occurrence, $oldSchedule, $newSchedule));
            }
        }

        // Annuler toutes les occurrences futures
        $futureOccurrences->each(function ($occurrence) {
            $occurrence->update(['is_cancelled' => true]);
        });

        // Mettre à jour le slot avec les nouvelles données
        $this->updateSlotWithoutScheduleChange($slot, $request);

        // Créer les nouvelles occurrences avec le nouvel horaire
        $this->generateNewOccurrences($slot);

        Log::info('Slot mis à jour avec changement d\'horaire', [
            'slot_id' => $slot->id,
            'old_schedule' => $oldSchedule,
            'new_schedule' => [
                'day' => $slot->day_of_week,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time
            ],
            'occurrences_cancelled' => $futureOccurrences->count(),
            'participants_notified' => $futureOccurrences->sum(function ($o) {
                return $o->attendees->count() + $o->monitors->count();
            })
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Le créneau a été modifié avec succès. ' . $futureOccurrences->count() . ' cours futurs ont été annulés et les participants ont été notifiés.',
            'occurrences_cancelled' => $futureOccurrences->count()
        ]);
    }

    /**
     * Générer les nouvelles occurrences pour un slot
     */
    private function generateNewOccurrences($slot)
    {
        // Supprimer les anciennes occurrences futures (déjà annulées)
        $slot->occurrences()
            ->where('date', '>', now())
            ->delete();

        // Générer les nouvelles occurrences pour les 6 prochains mois
        $startDate = now()->startOfWeek();
        $endDate = now()->addMonths(6);

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // Trouver le prochain jour de la semaine correspondant
            while ($currentDate->dayOfWeek != $slot->day_of_week) {
                $currentDate->addDay();
            }

            // Créer l'occurrence
            $slot->occurrences()->create([
                'date' => $currentDate->format('Y-m-d'),
                'is_cancelled' => false,
                'closing_notification_sent' => false
            ]);

            // Passer à la semaine suivante
            $currentDate->addWeek();
        }
    }
}
