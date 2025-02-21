<div class="container mx-auto p-6">
    <!-- Titre -->
    <div class="bg-[#17252A] text-[#DEF2F1] text-center text-2xl font-bold p-6 rounded-lg shadow-md mb-4">
        {{ $club->name }} - Tableau de bord
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-4">
        <div class="bg-[#2B7A78] text-[#DEF2F1] text-center p-6 rounded-lg shadow-md">
            <i data-lucide="users" class="text-5xl mb-2"></i>
            <h3 class="text-2xl font-semibold">Membres</h3>
            <p class="text-xl">{{ $totalMembers }}</p>
        </div>
        <div class="bg-[#3AAFA9] text-[#17252A] text-center p-6 rounded-lg shadow-md">
            <i data-lucide="user-check" class="text-5xl mb-2"></i>
            <h3 class="text-2xl font-semibold">Moniteurs</h3>
            <p class="text-xl">{{ $totalMonitors }}</p>
        </div>
        <div class="bg-[#DEF2F1] text-[#17252A] text-center p-6 rounded-lg shadow-md">
            <i data-lucide="calendar" class="text-5xl mb-2"></i>
            <h3 class="text-2xl font-semibold">Créneaux</h3>
            <p class="text-xl">{{ $totalSlots }}</p>
        </div>
    </div>

    <!-- Prochaines occurrences -->
    <div class="bg-[#FEFFFF] p-6 rounded-lg shadow-md mb-4">
        <h2 class="text-2xl font-semibold text-[#17252A] mb-3">Prochains créneaux</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @forelse ($nextOccurrences as $occurrence)
                <div class="bg-[#DEF2F1] text-[#17252A] p-4 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg relative">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-semibold">
                            {{ \Carbon\Carbon::createFromFormat('d-m-Y', $occurrence->date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </h4>
                        <p class="text-sm text-gray-700 flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>{{ $occurrence->slot->start_time }}
                        </p>
                    </div>

                    <div class="flex justify-between items-center mt-2 text-sm">
                        <p class="font-medium">👥 Participants: {{ $occurrence->attendees()->count() }}</p>
                        <span class="underline cursor-pointer text-[#2B7A78] font-bold"
                              onmouseover="showMonitorsTooltip(event, {{ json_encode($occurrence->monitors->map(fn($m) => $m->user->firstname . ' ' . $m->user->name)->toArray(), JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                              onmouseout="hideMonitorsTooltip()">
                            🎓 Moniteurs : {{ $occurrence->monitors()->count() }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">Aucune occurrence à venir.</p>
            @endforelse
        
        </div>
        <div id="occurrences-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                
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

    <!-- Menu rapide -->
    <div class="flex justify-center gap-6">
        <button 
            hx-get="/admin-club/members"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML"
            class="flex flex-col items-center w-40 h-40 bg-[#2B7A78] text-[#DEF2F1] hover:bg-[#3AAFA9] transition border rounded-lg shadow-lg p-6"
        >
            <i data-lucide="users" class="text-4xl mb-2"></i>
            <span class="text-lg font-bold">Gérer Membres</span>
        </button>

        <button 
            hx-get="/admin-club/slots"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML"
            class="flex flex-col items-center w-40 h-40 bg-[#3AAFA9] text-[#17252A] hover:bg-[#2B7A78] transition border rounded-lg shadow-lg p-6"
        >
            <i data-lucide="calendar" class="text-4xl mb-2"></i>
            <span class="text-lg font-bold">Gérer Créneaux</span>
        </button>

        <button 
            hx-get="/admin-club/settings"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML"
            class="flex flex-col items-center w-40 h-40 bg-[#17252A] text-[#DEF2F1] hover:bg-[#3AAFA9] transition border rounded-lg shadow-lg p-6"
        >
            <i data-lucide="settings" class="text-4xl mb-2"></i>
            <span class="text-lg font-bold">Paramètres</span>
        </button>
    </div>

    <!-- Contenu dynamique -->
    <div id="main-adminclub" class="mt-6"></div>
</div>

<!-- Tooltip -->
<div id="tooltip" class="hidden absolute bg-[#17252A] text-[#DEF2F1] text-sm rounded-lg px-3 py-2 shadow-lg transition-opacity duration-300"></div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    function showMonitorsTooltip(event, monitors) {
        const tooltip = document.getElementById('tooltip');
        if (monitors.length === 0) {
            tooltip.innerHTML = "Aucun moniteur inscrit";
        } else {
            tooltip.innerHTML = monitors.join("<br>");
        }
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
</script>
