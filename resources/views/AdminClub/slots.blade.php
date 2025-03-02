@php
    $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
@endphp

<div class="bg-[#17252A] text-[#DEF2F1] p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Vos créneaux</h2>

    <!-- Liste des slots -->
    <div class="space-y-4">
        @foreach ($slots as $slot)
            <div class="bg-[#2B7A78] rounded-lg shadow-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-[#DEF2F1]">{{ $slot->name }}</h3>
                        <p class="mt-1 text-sm">{{ $slot->description }}</p>
                        <p class="mt-1 text-sm">
                            {{ $days[$slot->day_of_week - 1] }} — de {{ $slot->start_time }} à {{ $slot->end_time }}
                        </p>
                        <p class="mt-1 text-sm">{{ $slot->capacity }} places</p>
                        @if($slot->is_restricted)
                            <p class="mt-1 text-xs font-bold text-red-400">
                                <i class="fas fa-lock"></i> Slot restreint
                            </p>
                        @endif
                    </div>
                    <div class="mt-4 sm:mt-0 flex gap-3">
                        <!-- Bouton Modifier -->
                        <button 
                            class="bg-[#3AAFA9] hover:bg-[#3AAFA9]/80 text-[#17252A] font-bold py-2 px-4 rounded transition"
                            hx-get="/admin-club/slots/edit/{{ $slot->id }}"
                            hx-target="#main-adminclub" 
                            hx-swap="innerHTML"
                        >
                            Modifier
                        </button>

                        <!-- Bouton Supprimer -->
                        <button
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition"
                            hx-trigger="confirmed"
                            hx-get="/admin-club/slots/delete/{{ $slot->id }}"
                            hx-target="#main-adminclub" 
                            hx-swap="innerHTML"
                            onClick="
                                Swal.fire({
                                    title: 'Êtes-vous sûr ?',
                                    text: 'Cela supprimera définitivement ce créneau ainsi que toutes les inscriptions !',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Oui',
                                    cancelButtonText: 'Annuler'
                                }).then((result)=>{
                                    if (result.isConfirmed) {
                                        htmx.trigger(this, 'confirmed');
                                    }
                                })"
                        >
                            Supprimer
                        </button>

                        <!-- Bouton pour gérer la whitelist si le slot est restreint -->
                        @if($slot->is_restricted)
                            <button 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition"
                                hx-get="/admin-club/slots/{{ $slot->id }}/whitelist"
                                hx-target="#whitelist-modal-container"
                                hx-swap="innerHTML"
                            >
                                Gérer accès
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bouton d'ajout -->
    <div class="mt-6">
        <button 
            class="bg-[#3AAFA9] hover:bg-[#3AAFA9]/80 text-[#17252A] font-bold py-2 px-4 rounded transition"
            hx-get="/admin-club/slots/new"
            hx-target="#main-adminclub" 
            hx-swap="innerHTML"
        >
            Ajouter un cours
        </button>
    </div>
</div>

<!-- Conteneur de la modale whitelist (initialement caché) -->
<!-- Conteneur modal (initialement vide) -->
<div id="whitelist-modal-container" 
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
</div>
