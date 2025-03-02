<div id="slot-{{ $slotOccurence->id }}" class="bg-[#DEF2F1] text-[#17252A] p-4 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg relative">
    <div class="flex justify-between items-center">
        <h4 class="text-lg font-semibold">
            {{ \Carbon\Carbon::createFromFormat('Y-m-d', $slotOccurence->date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </h4>
        <div class="flex items-center">
            <span class="text-xs bg-[#DEF2F1] text-[#17252A] rounded-full px-2 py-1">
                {{ $slotOccurence->slot->name }}
            </span>
            @if ($slotOccurence->slot->is_restricted)
                <span class="ml-2 text-xs bg-red-500 text-white rounded-full px-2 py-1">
                    Privé
                </span>
            @endif
        </div>
    </div>

    <p class="text-sm text-gray-700 flex items-center">
        <i data-lucide="clock" class="w-4 h-4 mr-1"></i>{{ $slotOccurence->slot->start_time }}
    </p>

    <div class="flex justify-between items-center mt-2 text-sm">
        <!-- Texte cliquable pour afficher la modal des participants -->
        <p class="font-medium underline cursor-pointer"
            hx-get="/admin-club/slots/{{ $slotOccurence->id }}/participants"
            hx-target="#participants-modal-container"
            hx-swap="innerHTML">
            👥 Participants : {{ $slotOccurence->attendees()->count() }} / {{ $slotOccurence->slot->capacity }}
        </p>
        <!-- Nombre de moniteurs avec tooltip -->
        <span class="underline cursor-pointer text-[#2B7A78] font-bold"
              onmouseover="showMonitorsTooltip(event, {{ json_encode($slotOccurence->monitors->map(fn($m) => $m->user->firstname.' '.$m->user->name)->toArray(), JSON_HEX_APOS | JSON_HEX_QUOT) }})"
              onmouseout="hideMonitorsTooltip()">
            🎓 Moniteurs : {{ $slotOccurence->monitors()->count() }}
        </span>
    </div>

    <!-- Alerte si le nombre de moniteurs est insuffisant -->
    @if (!is_null($slotOccurence->slot->alert_monitors) && $slotOccurence->monitors()->count() < $slotOccurence->slot->alert_monitors)
        <p class="mt-2 text-red-500 text-sm font-semibold">
            Attention : nombre de moniteurs insuffisant (minimum requis : {{ $slotOccurence->slot->alert_monitors }})
        </p>
    @endif

    @if ($slotOccurence->is_cancelled)
        <p class="mt-2 text-red-500 text-sm font-semibold">
            ❌ Annulé : {{ $slotOccurence->cancellation->reason }}
        </p>
    @else
        <button onclick="openCancelModal({{ $slotOccurence->id }})" class="mt-2 w-full bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 rounded-lg transition">
            Annuler
        </button>
    @endif
</div>
