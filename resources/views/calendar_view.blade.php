<div id="calendar-view" class="p-4">
    <!-- Navigation des mois -->
    <div class="flex items-center justify-between mb-6">
        <button 
            class="flex items-center justify-center w-10 h-10 rounded-full bg-[#3AAFA9] text-white hover:bg-[#2B7A78] transition-colors" 
            hx-get="{{ route('calendar.index', ['month' => $prev_month]) }}" 
            hx-target="#calendar-view" 
            hx-swap="outerHTML">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <h2 class="text-2xl font-bold text-[#17252A]">
            {{ ucfirst($current_month_name) }} {{ $current_year }}
        </h2>
        <button 
            class="flex items-center justify-center w-10 h-10 rounded-full bg-[#3AAFA9] text-white hover:bg-[#2B7A78] transition-colors" 
            hx-get="{{ route('calendar.index', ['month' => $next_month]) }}" 
            hx-target="#calendar-view" 
            hx-swap="outerHTML">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- En-tête des jours -->
    <div class="grid grid-cols-7 text-center text-sm font-semibold text-[#17252A] mb-2">
        <div>Lun</div>
        <div>Mar</div>
        <div>Mer</div>
        <div>Jeu</div>
        <div>Ven</div>
        <div>Sam</div>
        <div>Dim</div>
    </div>

    <!-- Tooltip Moniteurs -->
    <div id="tooltip" class="hidden absolute bg-[#17252A] text-[#DEF2F1] z-500 text-sm rounded-lg px-3 py-2 shadow-lg transition-opacity duration-300"></div>

    <!-- Conteneur de la modal pour les participants -->
    <div id="participants-modal-container" class="hidden fixed inset-0 z-500 flex items-center justify-center bg-black bg-opacity-50"></div>

    <!-- Grille du calendrier -->
    <div class="grid grid-cols-7 gap-1">
        @foreach ($calendar_days as $day)
            @php 
                $dayDate = \Carbon\Carbon::parse($day['date']); 
                $isCurrentMonth = ($dayDate->month == $current_month);
            @endphp
            <div class="{{ $isCurrentMonth ? 'bg-[#FEFFFF] text-[#17252A]' : 'bg-gray-200 text-gray-500' }} 
                        rounded-lg p-2 text-xs md:text-sm min-h-[80px] md:min-h-[100px] relative border border-gray-200">
                <div class="font-bold">{{ $day['number'] }}</div>
                @foreach ($day['events'] as $event)
                    @php
                        if ($event->slot->is_restricted) {
                            $allowedUserIds = $event->slot->whitelist->pluck('user_id');
                            if (!$allowedUserIds->contains(auth()->id())) {
                                continue;
                            }
                        }
                        $courseDateTime = \Carbon\Carbon::parse($event->date . ' ' . $event->slot->start_time);
                        $isPassed = $courseDateTime->isPast();
                    @endphp
                    @if ($event->is_cancelled)
                        <div class="mt-1 text-[10px] md:text-xs bg-red-100 text-blue-800 rounded px-1 py-0.5 cursor-pointer transition"
                            
                        >
                        {{ $event->slot->name }}<br>
                        {{ \Carbon\Carbon::parse($event->slot->start_time)->format('G\hi') }} – 
                        {{ \Carbon\Carbon::parse($event->slot->end_time)->format('G\hi') }}<br>
                        
                        <span class="text-red-800">Annulé</span>
                    @elseif ($isPassed)
                        <div class="mt-1 text-[10px] md:text-xs bg-gray-100 text-blue-800 rounded px-1 py-0.5 cursor-pointer transition"
                            
                        >
                        {{ $event->slot->name }}<br>
                        {{ \Carbon\Carbon::parse($event->slot->start_time)->format('G\hi') }} – 
                        {{ \Carbon\Carbon::parse($event->slot->end_time)->format('G\hi') }}<br>
                        
                        <span class="text-red-800">Terminé</span>
                    @else
                        <div class="relative mt-1 text-[10px] md:text-xs bg-[#2B7A78] text-white rounded px-1 py-0.5 cursor-pointer hover:bg-[#3AAFA9] transition"
                            hx-get="/calendar/slot/{{ $event->id }}"
                            hx-target="#modal-body"
                            hx-swap="innerHTML"
                            hx-on::after-request="openModal()"
                        >
                        {{ $event->slot->name }}<br>
                        {{ \Carbon\Carbon::parse($event->slot->start_time)->format('G\hi') }} – 
                        {{ \Carbon\Carbon::parse($event->slot->end_time)->format('G\hi') }}<br>                        
                    
                        @if (Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club')
                            👥 {{ $event->attendees()->count() }} / 🎓 {{ $event->monitors()->count() }}<br>
                            
                            @if (!is_null($event->slot->alert_monitors) && $event->monitors()->count() < $event->slot->alert_monitors)
                                <div class="absolute top-0 right-0 text-white text-[14px] font-bold px-2 py-1 rounded-br-lg shadow-md z-10">
                                    🚩
                                </div>
                            @endif
                        @endif
                    @endif

                    @if ($event->attendees()->where('user_id', auth()->id())->exists())
                        <div class="mt-1 w-full text-center px-2 py-1 text-xs font-bold bg-gray-300 text-[#17252A] rounded">
                            👥 Inscrit
                        </div>
                    @elseif ($event->monitors()->where('user_id', auth()->id())->exists())
                        <div class="mt-1 w-full text-center px-2 py-1 text-xs font-bold bg-gray-300 text-[#17252A] rounded">
                            🎓 Moniteur
                        </div>
                    @else
                        <div class="mt-1 w-full text-center px-2 py-1 text-xs font-bold bg-gray-300 text-[#17252A] rounded">
                            ❌ Non inscrit
                        </div>
                    @endif

                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<div id="modal-overlay" 
     class="hidden fixed inset-0 z-50 flex items-center justify-center"
     style="background-color: rgba(0, 0, 0, 0.4);" 
     onclick="closeModal(event)">
    <div id="modal-content" 
         class="bg-white w-11/12 max-w-md p-2 rounded-lg shadow-lg transform scale-0 transition-transform duration-300"
         onclick="event.stopPropagation()">
        <button class="w-10 h-10 absolute -top-10 -right-10 rounded-full bg-[#3AAFA9] text-white hover:text-gray-800 text-2xl font-bold"
                onclick="closeModal()">
            &times;
        </button>
        <div id="modal-body"></div>
    </div>
</div>

<script>
    // Rendre la fonction globale
    window.openModal = function() {
        const overlay = document.getElementById('modal-overlay');
        const modal   = document.getElementById('modal-content');

        overlay.classList.remove('hidden');
        modal.classList.remove('scale-0');
        modal.classList.add('scale-100');
    }

    window.closeModal = function(event) {
        if (!event || event.target.id === 'modal-overlay') {
            const overlay = document.getElementById('modal-overlay');
            const modal   = document.getElementById('modal-content');
            modal.classList.remove('scale-100');
            modal.classList.add('scale-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }

    document.body.addEventListener('htmx:afterSwap', (event) => {
        // On ne relance que pour le conteneur concerné
        if (event.detail.target.id === "app") {
            lucide.createIcons();
        }
    });

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

    // Si on clique sur le conteneur modal (fond semi-transparent), on ferme la modal
    document.getElementById('participants-modal-container').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    
</script>