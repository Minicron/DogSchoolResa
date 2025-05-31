<div class="container mx-auto p-6">
    <!-- Titre -->
    <div class="bg-[#17252A] text-[#DEF2F1] text-center text-2xl font-bold p-6 rounded-lg shadow-md mb-4">
        {{ $club->name }} - Tableau de bord
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-4">
        <div class="bg-[#2B7A78] text-[#DEF2F1] text-center p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Membres : {{ $totalMembers }}</h3>
        </div>
        <div class="bg-[#3AAFA9] text-[#17252A] text-center p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Moniteurs : {{ $totalMonitors }}</h3>
        </div>
        <div class="bg-[#DEF2F1] text-[#17252A] text-center p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Créneaux : {{ $totalSlots }}</h3>
        </div>
    </div>

    <!-- Menu rapide -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-4">
        <button 
            hx-get="/admin-club/members"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML"
            class="flex flex-col items-center justify-center gap-2 bg-[#2B7A78] text-[#DEF2F1] hover:bg-[#3AAFA9] transition rounded-lg shadow-lg p-6"
        >
            <i data-lucide="users" class="text-4xl"></i>
            <span class="text-2xl font-bold">Gestion des membres</span>
        </button>

        <button 
            hx-get="/admin-club/slots"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML"
            class="flex flex-col items-center justify-center gap-2 bg-[#3AAFA9] text-[#17252A] hover:bg-[#2B7A78] transition rounded-lg shadow-lg p-6"
        >
            <i data-lucide="calendar" class="text-4xl"></i>
            <span class="text-2xl font-bold">Gestion des créneaux</span>
        </button>

        <button 
            hx-get="/admin-club/settings"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML"
            class="flex flex-col items-center justify-center gap-2 bg-[#DEF2F1] text-[#17252A] hover:bg-[#3AAFA9] transition rounded-lg shadow-lg p-6"
        >
            <i data-lucide="settings" class="text-4xl"></i>
            <span class="text-2xl font-bold">Paramètres du club</span>
        </button>
    </div>

    <!-- Contenu dynamique -->
    <div id="main-adminclub" class="mt-6"></div>
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

    // Si on clique sur le conteneur modal (fond semi-transparent), on ferme la modal
    document.getElementById('participants-modal-container').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    
</script>
