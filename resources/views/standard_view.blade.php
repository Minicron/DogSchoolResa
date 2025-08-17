<!-- Vue standard (grid) pour l'affichage des créneaux -->
<div class="py-12">        
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Section heropanel -->
        @include('components.heropanel', ['nextSlot' => $nextSlot])

        <!-- Section header -->
        @if ($slotOccurences->isEmpty())
            <div class="bg-[#DEF2F1] p-6 rounded-lg shadow-lg mb-6">
                <h3 class="text-xl font-semibold text-[#17252A]">Aucun rendez-vous pour l'instant</h3>
            </div>
        @else
            <div class="bg-[#DEF2F1] p-6 rounded-lg shadow-lg mb-6">
                <h3 class="text-xl font-semibold text-[#17252A]">Prochains rendez-vous</h3>
            </div>
        @endif

        <!-- Tooltip Moniteurs - seulement pour les moniteurs et admins -->
        @if (Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club')
            <div id="tooltip" class="hidden absolute bg-[#17252A] text-[#DEF2F1] z-50 text-sm rounded-lg px-3 py-2 shadow-lg transition-opacity duration-300"></div>
        @endif

        <!-- Conteneur de la modal pour les participants -->
        <div id="participants-modal-container" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"></div>

        <!-- Modal d'annulation - seulement pour les admins -->
        @if (Auth::user()->role == 'admin-club')
            <div id="cancel-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirmer l'annulation</h3>
                    <p class="text-gray-600 mb-4">Êtes-vous sûr de vouloir annuler ce cours ?</p>
                    <form id="cancel-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="slot-occurence-id" name="slot_occurence_id">
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition">
                            Confirmer l'annulation
                        </button>
                    </form>
                    <button onclick="closeCancelModal()" class="mt-2 w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition">
                        Fermer
                    </button>
                </div>
            </div>
        @endif



        <!-- Prochaines occurrences -->
        <div id="occurrences-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($nextOccurrences as $slotOccurence)
                @php
                    // Filtrer les slots restreints
                    if ($slotOccurence->slot->is_restricted) {
                        $allowedUserIds = $slotOccurence->slot->whitelist->pluck('user_id');
                        if (!$allowedUserIds->contains(auth()->id())) {
                            continue;
                        }
                    }
                    $isRegisteredAsMember = $slotOccurence->attendees()->where('user_id', auth()->id())->exists();
                    $isRegisteredAsMonitor = $slotOccurence->monitors()->where('user_id', auth()->id())->exists();
                    $courseDateTime = $slotOccurence->date->copy()->setTimeFromTimeString($slotOccurence->slot->start_time);
                    $isPassed = $courseDateTime->isPast();
                    
                    // Utiliser les nouvelles méthodes du modèle pour la clôture des inscriptions
                    $isRegistrationClosed = $slotOccurence->isRegistrationClosed();
                    $registrationStatus = $slotOccurence->getRegistrationStatus();
                @endphp
                @include('AdminClub.partials.slot_tile', ['slotOccurence' => $slotOccurence])
            @endforeach
        </div>
        
        <!-- Bouton "Voir plus" -->
        <div class="mt-4 text-left">
            <button 
                id="load-more-button"
                hx-get="{{ route('admin.club.loadMoreOccurrences') }}" 
                hx-target="#occurrences-container" 
                hx-swap="beforeend"
                hx-trigger="click"
                hx-vals='{"offset": 6}'
                class="text-[#2B7A78] font-semibold hover:text-[#3AAFA9] transition"
            >
                Voir plus ⬇️
            </button>           
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>

    document.body.addEventListener('htmx:afterSwap', (event) => {
        // On ne relance que pour le conteneur concerné
        if (event.detail.target.id === "app") {
            lucide.createIcons();
        }
    });

    @if (Auth::user()->role == 'admin-club')
    function openCancelModal(slotOccurenceId) {
        document.getElementById('slot-occurence-id').value = slotOccurenceId;
        const form = document.getElementById('cancel-form');
        form.setAttribute('hx-post', `/admin-club/occurence/${slotOccurenceId}/cancel`);
        form.setAttribute('hx-target', `#slot-${slotOccurenceId}`);
        // Forcer htmx à reprocesser le formulaire afin de prendre en compte les nouveaux attributs
        htmx.process(form);
        document.getElementById('cancel-modal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancel-modal').classList.add('hidden');
    }
    @endif

    @if (Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club')
    function showMonitorsTooltip(event, monitors) {
        const tooltip = document.getElementById('tooltip');
        tooltip.innerHTML = monitors.length === 0 ? "Aucun moniteur inscrit" : monitors.join("<br>");
        tooltip.style.left = `${event.pageX + 10}px`;
        tooltip.style.top = `${event.pageY - 40}px`;
        tooltip.classList.remove('hidden');
        tooltip.classList.add('opacity-100');
    }

    function hideMonitorsTooltip() {
        const tooltip = document.getElementById('tooltip');
        tooltip.classList.add('hidden');
        tooltip.classList.remove('opacity-100');
    }
    @endif

    // Si on clique sur le conteneur modal (fond semi-transparent), on ferme la modal
    document.getElementById('participants-modal-container').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    
</script>