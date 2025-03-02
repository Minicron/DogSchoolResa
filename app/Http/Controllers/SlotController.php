<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;
use App\Models\SlotOccurence;
use App\Models\SlotOccurenceAttendee;

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
            $slot->capacity = request()->capacity;
            $slot->alert_monitors = request()->alert_monitors;
            $slot->auto_close = request()->has('auto_close');
            $slot->close_duration = $slot->auto_close ? request()->close_duration : null;
            $slot->is_restricted = request()->has('is_restricted');
            $slot->save();

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

            // Update the slot
            $slot->name = request()->name;
            $slot->description = request()->description;
            $slot->location = request()->location;
            $slot->day_of_week = request()->day_of_week;
            $slot->start_time = request()->time_start;
            $slot->end_time = request()->time_end;
            $slot->capacity = request()->capacity;
            $slot->save();

            // TODO : Update the slot's occurences start_time and end_time
            // TODO : What to do with the attendees ?

            $slots = Slot::all();

            return view('AdminClub.slots', ['slots' => $slots]);
        }

        return view('slot.edit', ['slot' => $slot]);
    }
}
