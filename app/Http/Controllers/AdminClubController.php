<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use App\Models\Slot;
use App\Models\User;
use App\Models\UserInvitation;
use App\Models\SlotOccurence;
use Carbon\Carbon;

class AdminClubController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $club = auth()->user()->club;

        if (!$club) {
            return redirect()->route('home')->with('error', 'Aucun club associé.');
        }

        // Récupérer les informations nécessaires pour le Dashboard
        $totalMembers = $club->members()->count();
        $totalMonitors = $club->monitors()->count();
        $totalSlots = $club->slots()->count();

        // Filtrer les occurrences pour ne prendre que celles dans le futur
        $nextOccurrencesQuery = $club->upcomingOccurrences()
            ->whereDate('date', '>=', now()->format('Y-m-d'))
            ->with(['slot'])
            ->limit(9);

        $nextOccurrences = $nextOccurrencesQuery->get()->sortBy(function($occurrence) {
            return Carbon::createFromFormat('Y-m-d', $occurrence->date);
        });

        return view('AdminClub.dashboard', compact(
            'club', 'totalMembers', 'totalMonitors', 'totalSlots', 'nextOccurrences'
        ));
    }

    public function loadMoreOccurrences(Request $request)
    {
        $club = auth()->user()->club;

        if (!$club) {
            return response()->json(['error' => 'Aucun club associé.'], 403);
        }


        $offset = $request->input('offset', 9); // Nombre d'occurrences déjà chargées
        $limit = 6; // Nombre d'occurrences à charger par requête

        $nextOccurrences = $club->upcomingOccurrences()
            ->with(['slot'])
            ->get()
            ->sortBy(function($occurrence) {
                return Carbon::createFromFormat('Y-m-d', $occurrence->date);
            })
            ->slice($offset, $limit);

        return view('AdminClub.partials.occurrences', compact('nextOccurrences'))->render();
    }

    /**
     * Display the slots.
     *
     * @return \Illuminate\View\View
     */
    public function slots($idClub = null)
    {
        if (!isset($idClub)) {
            $idClub = Auth::user()->club_id;
        }

        $slots = Slot::where('club_id', $idClub)->get();

        return view('AdminClub.slots', ['slots' => $slots]);
    }

    /**
     * Display the members of a club
     *
     * @return \Illuminate\View\View
     */
    public function members($idClub = null)
    {
        if (!isset($idClub)) {
            $idClub = Auth::user()->club_id;
        }
        
        $members = User::where('club_id', $idClub)->get();
    
        return view('AdminClub.members', ['members' => $members]);
    }

    /**
     * Display the members of a club
     *
     * @return \Illuminate\View\View
     */
    public function invite($idClub = null)
    {
        if (!isset($idClub)) {
            $idClub = Auth::user()->club_id;
        }

        // Manage the user creation
        if (request()->isMethod('post')) {

            // Check if the user already exists
            $user = User::where('email', request()->email)->first();
            if ($user) {
                $tempUser = new User();
                $tempUser->name = request()->name;
                $tempUser->surname = request()->surname;
                $tempUser->email = request()->email;
                $tempUser->role = request()->role;
                return view('User.invite' , ['idClub' => $idClub, 'user' => $tempUser, 'error' => 'EMAIL_ALREADY_EXISTS']);
            }

            // Generate token for the invitation
            $token = bin2hex(random_bytes(16));

            // Create the user entity
            $userInvitation = New UserInvitation();
            $userInvitation->name = request()->name;
            $userInvitation->firstname = request()->firstname;
            $userInvitation->email = request()->email;
            $userInvitation->role = request()->role;
            $userInvitation->club_id = $idClub;
            $userInvitation->token = $token;
            $userInvitation->is_sent = false;
            $userInvitation->is_accepted = false;
            $userInvitation->save();

            // Send the invitation email
            // TODO

            $members = User::where('club_id', $idClub)->get();    
            return view('AdminClub.members', ['members' => $members]);
        }

        return view('User.invite' , ['idClub' => $idClub]);
    }

    public function occurrenceHistory($id)
    {
        $occurence = SlotOccurence::with('histories')->find($id);

        if (!$occurence) {
            return response()->json(['error' => 'Occurence introuvable.'], 404);
        }

        return view('AdminClub.partials.occurence-history', compact('occurence'));
    }
}
