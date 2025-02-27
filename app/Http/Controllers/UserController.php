<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInvitation;
use App\Models\User;
use App\Models\Club;
use App\Models\Slot;
use App\Models\SlotOccurence;
use App\Models\SlotOccurenceAttendee;
use App\Models\SlotOccurenceMonitor;
use App\Models\SlotOccurenceHisto;

class UserController extends Controller
{
    
    public function index()
    {
        // Check if user is authenticated
        if (auth()->check()) {

            // Get user's club
            $club_id = auth()->user()->club_id;
            $club = Club::find($club_id);

            // Get club's slots
            $slots = Slot::where('club_id', $club_id)->get();

            // Get slot's futur occurences
            $slotOccurences = SlotOccurence::whereIn('slot_id', $slots->pluck('id'))
                ->where('date', '>=', date('Y-m-d'))
                ->orderBy('date', 'asc')
                ->limit(6) 
                ->get();


            return view('home', ['club' => $club, 'slots' => $slots, 'slotOccurences' => $slotOccurences]);
        }

        return view('home');
    }

    public function register(SlotOccurence $slotOccurence)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est déjà inscrit comme moniteur
        $isRegisteredAsMonitor = SlotOccurenceMonitor::where('slot_occurence_id', $slotOccurence->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($isRegisteredAsMonitor) {
            return back()->with('error', 'Vous êtes déjà inscrit à ce créneau en tant que moniteur.');
        }

        // Vérifier si l'utilisateur est déjà inscrit comme membre
        $isAlreadyRegistered = SlotOccurenceAttendee::where('slot_occurence_id', $slotOccurence->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($isAlreadyRegistered) {
            return back()->with('error', 'Vous êtes déjà inscrit à ce créneau.');
        }

        // Vérifier si le créneau est complet
        if ($slotOccurence->is_full) {
            return back()->with('error', 'Ce créneau est complet.');
        }

        // Enregistrer l'utilisateur comme participant à ce créneau
        SlotOccurenceAttendee::create([
            'slot_occurence_id' => $slotOccurence->id,
            'user_id' => auth()->id(),
        ]);

        SlotOccurenceHisto::create([
            'slot_occurence_id' => $slotOccurence->id,
            'user_id' => auth()->id(),
            'action' => SlotOccurenceHisto::ACTION_ATTENDEE_ADDED,
            'details' => "Inscription de l'adhérent {$user->firstname} {$user->name}"
        ]);

        return back()->with('success', 'Inscription réussie !');
    }

    public function unregister(SlotOccurence $slotOccurence)
    {
        // Vérifier si l'utilisateur est inscrit
        $attendee = $slotOccurence->attendees()->where('user_id', auth()->id())->first();

        if (!$attendee) {
            return back()->with('error', 'Vous n\'êtes pas inscrit à ce créneau.');
        }

        // Supprimer l'inscription
        $attendee->delete();

        SlotOccurenceHisto::create([
            'slot_occurence_id' => $slotOccurence->id,
            'user_id' => auth()->id(),
            'action' => SlotOccurenceHisto::ACTION_ATTENDEE_REMOVED,
            'details' => "Retrait de l'adhérent {$user->firstname} {$user->name}"
        ]);

        return back()->with('success', 'Désinscription réussie.');
    }

    public function registerAsMonitor(SlotOccurence $slotOccurence)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est déjà inscrit comme membre
        $isRegisteredAsMember = SlotOccurenceAttendee::where('slot_occurence_id', $slotOccurence->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($isRegisteredAsMember) {
            return back()->with('error', 'Vous êtes déjà inscrit à ce créneau en tant que membre.');
        }

        // Vérifier si l'utilisateur est déjà inscrit comme moniteur
        $isAlreadyRegistered = SlotOccurenceMonitor::where('slot_occurence_id', $slotOccurence->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($isAlreadyRegistered) {
            return back()->with('error', 'Vous êtes déjà inscrit à ce créneau en tant que moniteur.');
        }

        // Enregistrer l'utilisateur comme moniteur à ce créneau
        SlotOccurenceMonitor::create([
            'slot_occurence_id' => $slotOccurence->id,
            'user_id' => auth()->id(),
        ]);

        SlotOccurenceHisto::create([
            'slot_occurence_id' => $slotOccurence->id,
            'user_id' => auth()->id(),
            'action' => SlotOccurenceHisto::ACTION_MONITOR_ASSIGNED,
            'details' => "Inscription du moniteur {$user->firstname} {$user->name}"
        ]);

        return back()->with('success', 'Inscription en tant que moniteur réussie !');
    }

    public function unregisterAsMonitor(SlotOccurence $slotOccurence)
    {
        // Vérifier si l'utilisateur est inscrit comme moniteur
        $monitor = $slotOccurence->monitors()->where('user_id', auth()->id())->first();

        if (!$monitor) {
            return back()->with('error', 'Vous n\'êtes pas inscrit à ce créneau en tant que moniteur.');
        }

        // Supprimer l'inscription
        $monitor->delete();

        SlotOccurenceHisto::create([
            'slot_occurence_id' => $slotOccurence->id,
            'user_id' => auth()->id(),
            'action' => SlotOccurenceHisto::ACTION_MONITOR_REMOVED,
            'details' => "Retrait du moniteur {$user->firstname} {$user->name}"
        ]);

        return back()->with('success', 'Désinscription en tant que moniteur réussie.');
    }

    public function registerFromMail($token = null)
    {
        
        $userInvitation = UserInvitation::where('token', $token)->first();

        if (!$userInvitation) {
            return redirect()->route('login');
        }

        if (request()->isMethod('post')) {

            $user = new User();
            $user->name = request()->name;
            $user->firstname = request()->firstname;
            $user->email = request()->email;
            $user->password = bcrypt(request()->password);
            $user->role = 'user';
            $user->club_id = $userInvitation->club_id;
            $user->is_active = 1;
            $user->save();

            $userInvitation->is_accepted = true;
            $userInvitation->save();
            
            return redirect()->route('login');
        }

        return view('auth.register', ['userInvitation' => $userInvitation]);
        
    }
}
