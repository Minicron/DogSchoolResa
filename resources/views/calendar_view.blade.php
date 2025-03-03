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
                    <div class="mt-1 text-[10px] md:text-xs bg-blue-100 text-blue-800 rounded px-1 py-0.5 cursor-pointer hover:bg-blue-200 transition"
                        hx-get="{{ route('calendar.eventDetails', ['id' => $event->id]) }}"
                        hx-target="#modal-body"
                        hx-swap="innerHTML"
                        hx-on::after-request="openModal()"
                    >
                        {{ \Carbon\Carbon::parse($event->slot->start_time)->format('G\hi') }} – 
                        {{ \Carbon\Carbon::parse($event->slot->end_time)->format('G\hi') }}<br>
                        {{ $event->slot->name }}
                        @if ($event->attendees()->where('user_id', auth()->id())->exists())
                            <div class="text-green-700 font-bold">(Membre)</div>
                        @elseif ($event->monitors()->where('user_id', auth()->id())->exists())
                            <div class="text-blue-700 font-bold">(Moniteur)</div>
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
         class="bg-white w-11/12 max-w-md p-6 rounded-lg shadow-lg transform scale-0 transition-transform duration-300"
         onclick="event.stopPropagation()">
        <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl font-bold"
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
</script>