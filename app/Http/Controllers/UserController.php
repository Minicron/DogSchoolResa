<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UserInvitation;
use App\Models\User;
use App\Models\Club;
use App\Models\Slot;
use App\Models\SlotOccurence;
use App\Models\SlotOccurenceAttendee;
use App\Models\SlotOccurenceMonitor;
use App\Models\SlotOccurenceHisto;
use App\Services\EmailService;
use Carbon\Carbon;
use App\Models\WaitingList;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        if (auth()->check()) {

            // Récupérer le club de l'utilisateur
            $club_id = auth()->user()->club_id;
            $club = Club::find($club_id);

            // Récupérer les slots du club
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

            if (auth()->user()->calendar_view) {
                // --- Logique de la vue calendrier ---

                // Si un mois est fourni via GET, on le stocke en session
                if ($request->has('month')) {
                    session(['calendar_month' => $request->input('month')]);
                }

                // Sinon on vérifie si un mois est en session
                $month = session('calendar_month', Carbon::now()->format('Y-m'));
                $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
                $endOfMonth   = Carbon::parse($month . '-01')->endOfMonth();

                // Récupérer toutes les occurrences programmées durant le mois
                $slotOccurences = SlotOccurence::with('slot.whitelist')
                    ->whereIn('slot_id', $slots->pluck('id'))
                    ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->orderBy('date', 'asc')
                    ->get();

                // Construire la grille du calendrier
                $calendar_days = [];
                // Début de la grille : début de la semaine contenant le 1er du mois (supposé lundi)
                $current = $startOfMonth->copy()->startOfWeek();
                $endCalendar = $endOfMonth->copy()->endOfWeek();
                while ($current->lte($endCalendar)) {
                    // Récupérer les événements programmés ce jour
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

                $current_month_name = $startOfMonth->locale('fr')->isoFormat('MMMM');
                $current_year       = $startOfMonth->year;
                $prev_month         = $startOfMonth->copy()->subMonth()->format('Y-m');
                $next_month         = $startOfMonth->copy()->addMonth()->format('Y-m');
                $current_month      = $startOfMonth->month;

                return view('home', [
                    'club'               => $club,
                    'slots'              => $slots,
                    'calendar_days'      => $calendar_days,
                    'current_month_name' => $current_month_name,
                    'current_year'       => $current_year,
                    'prev_month'         => $prev_month,
                    'next_month'         => $next_month,
                    'nextSlot'           => $nextSlot,
                    'current_month'      => $current_month
                    // Vous pouvez ajouter d'autres variables spécifiques au calendrier ici
                ]);

            } else {

                // --- Vue standard ---
                session()->forget('calendar_month');

                // Récupérer les informations nécessaires pour le Dashboard
                //$totalMembers = $club->members()->count();
                //$totalMonitors = $club->monitors()->count();
                //$totalSlots = $club->slots()->count();

                // Filtrer les occurrences pour ne prendre que celles dans le futur
                $nextOccurrencesQuery = $club->upcomingOccurrences()
                    ->whereDate('date', '>=', now()->format('Y-m-d'))
                    ->with(['slot'])
                    ->limit(9);

                $nextOccurrences = $nextOccurrencesQuery->get()->sortBy('date');

                $slotOccurences = SlotOccurence::with('slot.whitelist')
                    ->whereIn('slot_id', $slots->pluck('id'))
                    ->where('date', '>=', date('Y-m-d'))
                    ->orderBy('date', 'asc')
                    ->limit(12)
                    ->get();
                

                return view('home', [
                    'club'           => $club,
                    //'totalMembers'   => $totalMembers,
                    //'totalMonitors'  => $totalMonitors,
                    //'totalSlots'     => $totalSlots,
                    'nextOccurrences'=> $slotOccurences,
                    'slots'          => $slots,
                    'nextSlot'       => $nextSlot,
                    'slotOccurences' => $slotOccurences
                ]);
            }
        }

        return view('home');
    }

    public function toggleView(Request $request)
    {
        $user = auth()->user();
        // Récupérer la nouvelle préférence envoyée (0 ou 1) et la convertir en booléen
        $newPreference = filter_var($request->input('calendar_view'), FILTER_VALIDATE_BOOLEAN);
        $user->calendar_view = $newPreference;
        $user->save();
        // Retourner une redirection HTMX vers la page d'accueil
        return response('', 200)->header('HX-Redirect', route('home'));
    }
    
    public function register(SlotOccurence $slotOccurence)
    {
        $user = auth()->user();

        // Vérifier si les inscriptions sont closes
        if ($slotOccurence->isRegistrationClosed()) {
            return back()->with('error', 'Les inscriptions pour ce cours sont closes. Il n\'est plus possible de s\'inscrire.');
        }

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

        // Vérifier si l'utilisateur est déjà en liste d'attente
        $waitingListService = app(\App\Services\WaitingListService::class);
        if ($waitingListService->isUserWaiting($user, $slotOccurence)) {
            return back()->with('error', 'Vous êtes déjà en liste d\'attente pour ce créneau.');
        }

        // Vérifier si le créneau est complet
        if ($slotOccurence->isFull()) {
            // Ajouter en liste d'attente
            if ($waitingListService->addToWaitingList($user, $slotOccurence)) {
                return back()->with('success', 'Le cours est complet. Vous avez été ajouté en liste d\'attente. Vous recevrez un email dès qu\'une place se libère.');
            } else {
                return back()->with('error', 'Erreur lors de l\'ajout en liste d\'attente.');
            }
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
        $user = auth()->user();

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

        // Traiter la liste d'attente si des places se sont libérées
        $waitingListService = app(\App\Services\WaitingListService::class);
        $waitingListService->checkAndProcessWaitingList($slotOccurence);

        return back()->with('success', 'Désinscription réussie.');
    }

    /**
     * Ajouter un utilisateur en liste d'attente
     */
    public function addToWaitingList(SlotOccurence $slotOccurence)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est déjà inscrit
        $isAlreadyRegistered = SlotOccurenceAttendee::where('slot_occurence_id', $slotOccurence->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($isAlreadyRegistered) {
            return back()->with('error', 'Vous êtes déjà inscrit à ce créneau.');
        }

        // Vérifier si l'utilisateur est déjà en liste d'attente
        $waitingListService = app(\App\Services\WaitingListService::class);
        if ($waitingListService->isUserWaiting($user, $slotOccurence)) {
            return back()->with('error', 'Vous êtes déjà en liste d\'attente pour ce créneau.');
        }

        // Ajouter en liste d'attente
        if ($waitingListService->addToWaitingList($user, $slotOccurence)) {
            return back()->with('success', 'Vous avez été ajouté en liste d\'attente. Vous recevrez un email dès qu\'une place se libère.');
        } else {
            return back()->with('error', 'Erreur lors de l\'ajout en liste d\'attente.');
        }
    }

    /**
     * Retirer un utilisateur de la liste d'attente
     */
    public function removeFromWaitingList(SlotOccurence $slotOccurence)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est en liste d'attente
        $waitingListEntry = WaitingList::where('slot_occurence_id', $slotOccurence->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$waitingListEntry) {
            return back()->with('error', 'Vous n\'êtes pas en liste d\'attente pour ce créneau.');
        }

        // Supprimer de la liste d'attente
        $waitingListEntry->delete();

        return back()->with('success', 'Vous avez été retiré de la liste d\'attente.');
    }

    public function registerAsMonitor(SlotOccurence $slotOccurence)
    {
        $user = auth()->user();

        // Vérifier si les inscriptions sont closes
        if ($slotOccurence->isRegistrationClosed()) {
            return back()->with('error', 'Les inscriptions pour ce cours sont closes. Il n\'est plus possible de s\'inscrire.');
        }

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

        // Traiter la liste d'attente si la capacité a augmenté (mode dynamique)
        $waitingListService = app(\App\Services\WaitingListService::class);
        $waitingListService->checkAndProcessWaitingList($slotOccurence);

        return back()->with('success', 'Inscription en tant que moniteur réussie !');
    }

    public function unregisterAsMonitor(SlotOccurence $slotOccurence)
    {
        $user = auth()->user();
        
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

        // Vérifier la capacité dynamique et notifier les admins si nécessaire
        $this->checkDynamicCapacityAndNotifyAdmins($slotOccurence);

        // Traiter la liste d'attente si la capacité a diminué (mode dynamique)
        $waitingListService = app(\App\Services\WaitingListService::class);
        $waitingListService->checkAndProcessWaitingList($slotOccurence);

        return back()->with('success', 'Désinscription en tant que moniteur réussie.');
    }

    /**
     * Vérifier la capacité dynamique et notifier les admins si nécessaire
     */
    private function checkDynamicCapacityAndNotifyAdmins(SlotOccurence $slotOccurence)
    {
        // Vérifier si c'est un slot avec capacité dynamique
        if ($slotOccurence->slot->capacity_type !== 'dynamic') {
            return;
        }

        $monitorCount = $slotOccurence->monitors()->count();
        $attendeeCount = $slotOccurence->attendees()->count();
        $newCapacity = $monitorCount * 5;

        // Si on a plus de participants que la nouvelle capacité
        if ($attendeeCount > $newCapacity) {
            $excessCount = $attendeeCount - $newCapacity;
            
            // Notifier tous les admins du club
            $club = $slotOccurence->slot->club;
            $admins = User::where('club_id', $club->id)
                ->whereIn('role', ['admin', 'admin-club', 'super_admin'])
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\DynamicCapacityExceededNotification(
                    $slotOccurence, 
                    $monitorCount, 
                    $attendeeCount, 
                    $newCapacity,
                    $excessCount
                ));
            }
        }
    }

    public function registerFromMail($token = null)
    {
        $userInvitation = UserInvitation::where('token', $token)->first();

        if (!$userInvitation) {
            return redirect()->route('login')->with('error', 'Lien d\'invitation invalide ou expiré.');
        }

        if ($userInvitation->is_accepted) {
            return redirect()->route('login')->with('error', 'Cette invitation a déjà été utilisée.');
        }

        if (request()->isMethod('post')) {
            // Validation des données
            request()->validate([
                'name' => 'required|string|max:255',
                'firstname' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Vérifier que l'email correspond à l'invitation
            if (request()->email !== $userInvitation->email) {
                return back()->withErrors(['email' => 'L\'adresse email doit correspondre à celle de l\'invitation.']);
            }

            try {
                // Créer l'utilisateur
                $user = new User();
                $user->name = request()->name;
                $user->firstname = request()->firstname;
                $user->email = request()->email;
                $user->password = bcrypt(request()->password);
                $user->role = $userInvitation->role; // Utiliser le rôle de l'invitation
                $user->club_id = $userInvitation->club_id;
                $user->is_active = 1;
                $user->save();

                // Marquer l'invitation comme acceptée
                $userInvitation->is_accepted = true;
                $userInvitation->save();

                // Envoyer un email de bienvenue
                $club = Club::find($userInvitation->club_id);
                $emailService = app(\App\Services\EmailService::class);
                $emailService->sendWelcomeEmail($user, $club);
                
                return redirect()->route('home')->with('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter avec vos identifiants.');
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la création du compte utilisateur', [
                    'email' => request()->email,
                    'error' => $e->getMessage()
                ]);
                
                return back()->withErrors(['error' => 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.']);
            }
        }

        return view('auth.register', ['userInvitation' => $userInvitation]);
    }
}
