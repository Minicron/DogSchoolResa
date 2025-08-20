<div id="slot-{{ $slotOccurence->id }}" 
     class="bg-white text-[#17252A] p-4 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg relative flex flex-col h-full">

    @php
        // Définir les variables de clôture des inscriptions si elles ne sont pas déjà définies
        if (!isset($isRegistrationClosed)) {
            $isRegistrationClosed = $slotOccurence->isRegistrationClosed();
        }
        if (!isset($registrationStatus)) {
            $registrationStatus = $slotOccurence->getRegistrationStatus();
        }
        
        // Définir les variables d'inscription si elles ne sont pas déjà définies
        if (!isset($isRegisteredAsMember)) {
            $isRegisteredAsMember = $slotOccurence->attendees()->where('user_id', auth()->id())->exists();
        }
        if (!isset($isRegisteredAsMonitor)) {
            $isRegisteredAsMonitor = $slotOccurence->monitors()->where('user_id', auth()->id())->exists();
        }
        
        // Définir la variable de cours passé si elle n'est pas déjà définie
        if (!isset($isPassed)) {
            $courseDateTime = $slotOccurence->date->copy()->setTimeFromTimeString($slotOccurence->slot->start_time);
            $isPassed = $courseDateTime->isPast();
        }
    @endphp

    <!-- Contenu principal -->
    <div class="flex-grow">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-semibold">
                {{ \Carbon\Carbon::parse($slotOccurence->date)->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}
            </h4>
            <span class="text-xs bg-[#DEF2F1] text-[#17252A] rounded-full px-2 py-1">
                {{ $slotOccurence->slot->name }}
            </span>
            @if ($slotOccurence->slot->is_restricted)
                <span class="ml-2 text-xs bg-red-500 text-white rounded-full px-2 py-1">
                    Privé
                </span>
            @endif
        </div>

        <p class="text-sm text-gray-700 flex items-center mb-2">
            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>De {{ $slotOccurence->slot->start_time }} à {{ $slotOccurence->slot->end_time }}
        </p>

        @if (Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club')
            <div class="flex justify-between items-center mb-2 text-sm">
                <p class="font-medium underline cursor-pointer"
                    hx-get="/admin-club/slots/{{ $slotOccurence->id }}/participants"
                    hx-target="#participants-modal-container"
                    hx-swap="innerHTML">
                    👥 Participants : {{ $slotOccurence->attendees()->count() }} / 
                    @if($slotOccurence->slot->capacity_type === 'dynamic')
                        {{ $slotOccurence->slot->getCurrentCapacity($slotOccurence) ?? '∞' }}
                    @elseif($slotOccurence->slot->capacity_type === 'fixed')
                        {{ $slotOccurence->slot->capacity }}
                    @else
                        ∞
                    @endif
                    @if($slotOccurence->waitingList()->count() > 0)
                        <span class="text-orange-500">({{ $slotOccurence->waitingList()->count() }} en attente)</span>
                    @endif
                </p>
                <span class="underline cursor-pointer text-[#2B7A78] font-bold"
                      onmouseover="showMonitorsTooltip(event, {{ json_encode($slotOccurence->monitors->map(fn($m) => $m->user->firstname.' '.$m->user->name)->toArray(), JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                      onmouseout="hideMonitorsTooltip()">
                    🎓 Moniteurs : {{ $slotOccurence->monitors()->count() }}
                </span>
            </div>
        @endif



        @if ($slotOccurence->is_cancelled)
            <p class="text-red-500 text-sm font-semibold">
                ❌ Annulé : {{ $slotOccurence->cancellation->reason }}
            </p>
        @endif
    </div>

    <!-- Boutons (en bas de la tuile) -->
    <div class="mt-4 space-y-2">
        @if (!$slotOccurence->is_cancelled)
            @if ($isPassed)
                <p class="text-center text-red-600 font-bold">Cours terminé</p>
            @elseif ($isRegistrationClosed)
                <p class="text-center text-red-600 font-bold">Inscriptions clôturées</p>
            @else
                @php
                    $waitingListService = app(\App\Services\WaitingListService::class);
                    $isWaiting = $waitingListService->isUserWaiting(auth()->user(), $slotOccurence);
                @endphp
                
                @if (!$isRegisteredAsMember && !$isRegisteredAsMonitor && !$isWaiting)
                    @if ($slotOccurence->isFull())
                        <button onclick="openWaitingListModal({{ $slotOccurence->id }})" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg transition">
                            Cours complet - Liste d'attente
                        </button>
                    @else
                        <form action="{{ route('slot.register', $slotOccurence->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                @if (Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club')
                                    S'inscrire en tant que membre
                                @else
                                    S'inscrire
                                @endif
                            </button>
                        </form>
                    @endif
                @elseif ($isWaiting)
                    <div class="text-center">
                        <p class="text-orange-600 font-semibold mb-2">En liste d'attente</p>
                        <form action="{{ route('slot.waiting-list.remove', $slotOccurence->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition">
                                Se retirer de la liste
                            </button>
                        </form>
                    </div>
                @elseif ($isRegisteredAsMember)
                    <form action="{{ route('slot.unregister', $slotOccurence->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                            Se désinscrire en tant que membre
                        </button>
                    </form>
                @endif

                @if ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club'))
                    @if (!$isRegisteredAsMonitor)
                        <form action="{{ route('slot.register.monitor', $slotOccurence->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                S'inscrire en tant que moniteur
                            </button>
                        </form>
                    @else
                        <form action="{{ route('slot.unregister.monitor', $slotOccurence->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                Se désinscrire en tant que moniteur
                            </button>
                        </form>
                    @endif
                @endif
            @endif

            @if (Auth::user()->role == 'admin-club')
                <button onclick="openCancelModal({{ $slotOccurence->id }})"
                        class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 rounded-lg transition">
                    Annuler
                </button>
            @endif
        @endif
    </div>

    <!-- Badge d'inscription (coin) - seulement pour les moniteurs et admins -->
    @if (Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club')
        @if ($isRegisteredAsMonitor)
            <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                Inscrit moniteur
            </div>
        @elseif ($isRegisteredAsMember)
            <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                Inscrit membre
            </div>
        @endif
    @endif
</div>

<script>
    function openWaitingListModal(slotOccurrenceId) {
        // Créer le modal s'il n'existe pas
        if (!document.getElementById('waiting-list-modal')) {
            const modal = document.createElement('div');
            modal.id = 'waiting-list-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
            modal.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 mb-4">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Cours complet</h3>
                        <p class="text-gray-600 mb-4">Ce cours est actuellement complet. Voulez-vous être ajouté en liste d'attente ?</p>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-orange-800">
                                <strong>Comment ça marche :</strong><br>
                                • Vous serez automatiquement inscrit dès qu'une place se libère<br>
                                • Vous recevrez un email de confirmation<br>
                                • Vous pouvez annuler votre inscription en liste d'attente à tout moment
                            </p>
                        </div>
                        <form id="waiting-list-form" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg transition mb-2">
                                Oui, m'ajouter en liste d'attente
                            </button>
                        </form>
                        <button onclick="closeWaitingListModal()" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition">
                            Non, merci
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Configurer le formulaire et afficher le modal
        const form = document.getElementById('waiting-list-form');
        form.setAttribute('action', `/slot/waiting-list/${slotOccurrenceId}`);
        document.getElementById('waiting-list-modal').classList.remove('hidden');
    }

    function closeWaitingListModal() {
        const modal = document.getElementById('waiting-list-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function openCancelModal(slotOccurrenceId) {
        // Créer le modal d'annulation s'il n'existe pas
        if (!document.getElementById('cancel-modal')) {
            const modal = document.createElement('div');
            modal.id = 'cancel-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
            modal.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Annuler le cours</h3>
                        <p class="text-gray-600 mb-4">Êtes-vous sûr de vouloir annuler ce cours ? Cette action est irréversible.</p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-red-800">
                                <strong>Conséquences :</strong><br>
                                • Tous les participants seront notifiés par email<br>
                                • Le cours apparaîtra comme "Annulé"<br>
                                • Les inscriptions seront automatiquement supprimées
                            </p>
                        </div>
                        <form id="cancel-form" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="cancel-reason" class="block text-sm font-medium text-gray-700 mb-2">Raison de l'annulation (optionnel)</label>
                                <textarea id="cancel-reason" name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Ex: Mauvais temps, moniteur indisponible..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition mb-2">
                                Oui, annuler le cours
                            </button>
                        </form>
                        <button onclick="closeCancelModal()" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition">
                            Non, garder le cours
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Configurer le formulaire et afficher le modal
        const form = document.getElementById('cancel-form');
        form.setAttribute('action', `/admin-club/occurence/${slotOccurrenceId}/cancel`);
        
        // Gérer la soumission du formulaire avec fetch pour traiter la réponse JSON
        form.onsubmit = function(e) {
            e.preventDefault();
            
            const reason = document.getElementById('cancel-reason').value;
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fermer la modal d'annulation
                    closeCancelModal();
                    
                    // Fermer la modal du calendrier si elle existe
                    if (typeof closeModal === 'function') {
                        closeModal();
                    }
                    
                    // Afficher un message de succès
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Succès !',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Rafraîchir la page après le message
                            window.location.reload();
                        });
                    } else {
                        alert(data.message);
                        window.location.reload();
                    }
                } else {
                    // Afficher l'erreur
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Erreur',
                            text: data.error || 'Une erreur est survenue',
                            icon: 'error'
                        });
                    } else {
                        alert(data.error || 'Une erreur est survenue');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de l\'annulation',
                        icon: 'error'
                    });
                } else {
                    alert('Une erreur est survenue lors de l\'annulation');
                }
            });
        };
        
        document.getElementById('cancel-modal').classList.remove('hidden');
    }

    function closeCancelModal() {
        const modal = document.getElementById('cancel-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
