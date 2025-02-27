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
        <div id="occurrences-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($nextOccurrences as $occurrence)
                @include('AdminClub.partials.slot_tile', ['slotOccurence' => $occurrence])
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

    <!-- Tooltip Moniteurs -->
    <div id="tooltip" class="hidden absolute bg-[#17252A] text-[#DEF2F1] text-sm rounded-lg px-3 py-2 shadow-lg transition-opacity duration-300"></div>

    <!-- Modal d'annulation -->
    <div id="cancel-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center transition-opacity duration-300">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-semibold text-[#17252A] mb-3">Annuler une occurrence</h2>
            <form id="cancel-form" method="POST" hx-post="" hx-target="" hx-swap="outerHTML" hx-on::after-request="closeCancelModal()">
                @csrf
                <input type="hidden" name="slot_occurence_id" id="slot-occurence-id">
                
                <label class="block text-sm font-medium text-gray-700">Raison :</label>
                <select name="reason" class="w-full p-2 border rounded-lg mt-2">
                    <option value="Météo défavorable">Météo défavorable</option>
                    <option value="Manque de moniteurs">Manque de moniteurs</option>
                    <option value="Autre">Autre</option>
                </select>
                
                <button type="submit" class="mt-4 w-full bg-[#2B7A78] text-white font-semibold py-2 rounded-lg hover:bg-[#3AAFA9] transition">
                    Confirmer l'annulation
                </button>
            </form>
            <button onclick="closeCancelModal()" class="mt-2 w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- Script -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>

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
</script>
