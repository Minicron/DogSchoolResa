<div id="slot-{{ $slotOccurence->id }}" 
     class="bg-white text-[#17252A] p-4 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg relative flex flex-col h-full">

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
                    👥 Participants : {{ $slotOccurence->attendees()->count() }} / {{ $slotOccurence->slot->capacity }}
                </p>
                <span class="underline cursor-pointer text-[#2B7A78] font-bold"
                      onmouseover="showMonitorsTooltip(event, {{ json_encode($slotOccurence->monitors->map(fn($m) => $m->user->firstname.' '.$m->user->name)->toArray(), JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                      onmouseout="hideMonitorsTooltip()">
                    🎓 Moniteurs : {{ $slotOccurence->monitors()->count() }}
                </span>
            </div>
        @endif

        @if (Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club')
            @if (!is_null($slotOccurence->slot->alert_monitors) && $slotOccurence->monitors()->count() < $slotOccurence->slot->alert_monitors)
                <p class="mb-2 text-red-500 text-sm font-semibold">
                    Attention : nombre de moniteurs insuffisant (minimum requis : {{ $slotOccurence->slot->alert_monitors }})
                </p>
            @endif
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
            @if ($registrationClosed)
                <p class="text-center text-red-600 font-bold">Inscriptions clôturées</p>
            @else
                @if (!$isRegisteredAsMember && !$isRegisteredAsMonitor)
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

    <!-- Badge d'inscription (coin) -->
    @if ($isRegisteredAsMonitor)
        <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
            Inscrit moniteur
        </div>
    @elseif ($isRegisteredAsMember)
        <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
            Inscrit membre
        </div>
    @endif
</div>
