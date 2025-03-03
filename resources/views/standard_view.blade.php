<!-- Vue standard (grid) pour l'affichage des créneaux -->
<div class="py-12">        
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="bg-[#DEF2F1] p-6 rounded-lg shadow-lg mb-6">
            <h3 class="text-xl font-semibold text-[#17252A]">Prochains rendez-vous</h3>
        </div>

        <!-- Grid layout for the schedule slots -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
            @foreach ($slotOccurences->sortBy(function($slotOccurence) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $slotOccurence->date);
            }) as $slotOccurence)
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
                    $courseDateTime = \Carbon\Carbon::parse($slotOccurence->date . ' ' . $slotOccurence->slot->start_time);
                    $registrationClosed = false;
                    if ($slotOccurence->slot->auto_close && !is_null($slotOccurence->slot->close_duration)) {
                        $deadline = \Carbon\Carbon::now()->addHours($slotOccurence->slot->close_duration);
                        if ($deadline->greaterThan($courseDateTime)) {
                            $registrationClosed = true;
                        }
                    }
                @endphp

                <div id="slot-{{ $slotOccurence->id }}" class="bg-[#FEFFFF] text-[#17252A] p-6 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold">
                            {{ \Carbon\Carbon::parse($slotOccurence->date)->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}
                        </h4>
                        <span class="text-xs bg-[#DEF2F1] text-[#17252A] rounded-full px-2 py-1">
                            {{ $slotOccurence->slot->name }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mb-2">
                        Heure : De {{ $slotOccurence->slot->start_time }} à {{ $slotOccurence->slot->end_time }}
                    </p>

                    <div class="flex justify-between items-center text-sm">
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
                        @if ($registrationClosed)
                            <div class="p-4 bg-gray-200 rounded-lg text-center">
                                <p class="text-red-600 font-bold">Inscription terminée</p>
                                @if ($isRegisteredAsMember)
                                    <p class="text-green-600 font-semibold mt-2">Vous êtes inscrit(e) en tant que membre</p>
                                @else
                                    <p class="text-gray-600 font-semibold mt-2">Vous ne vous êtes pas inscrit(e)</p>
                                @endif
                                @if ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && !$isRegisteredAsMonitor)
                                    <form action="{{ route('slot.register.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                            S'inscrire en tant que moniteur
                                        </button>
                                    </form>
                                @elseif ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && $isRegisteredAsMonitor)
                                    <form action="{{ route('slot.unregister.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                            Se désinscrire en tant que moniteur
                                        </button>
                                    </form>
                                    <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                                        Inscrit en tant que moniteur
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="mt-2 text-sm text-gray-600">
                                Places disponibles : {{ $slotOccurence->slot->capacity - $slotOccurence->attendees()->count() }}
                            </p>

                            @if (!$isRegisteredAsMember && !$isRegisteredAsMonitor)
                                <form action="{{ route('slot.register', $slotOccurence->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                        S'inscrire en tant que membre
                                    </button>
                                </form>
                            @elseif ($isRegisteredAsMember)
                                <form action="{{ route('slot.unregister', $slotOccurence->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                        Se désinscrire en tant que membre
                                    </button>
                                </form>
                                <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                                    Inscrit en tant que membre
                                </div>
                            @endif

                            @if ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && !$isRegisteredAsMonitor)
                                <form action="{{ route('slot.register.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                        S'inscrire en tant que moniteur
                                    </button>
                                </form>
                            @elseif ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && $isRegisteredAsMonitor)
                                <form action="{{ route('slot.unregister.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                        Se désinscrire en tant que moniteur
                                    </button>
                                </form>
                                <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                                    Inscrit en tant que moniteur
                                </div>
                            @endif
                        @endif
                    @endif
            </div>
        @endforeach
    </div>
</div> 