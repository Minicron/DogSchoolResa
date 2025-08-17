<div id="calendar-view" class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 pt-16">
    <!-- En-tête avec navigation -->
    <div class="sticky top-16 z-20 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm">
        <div class="px-4 py-3">
            <!-- Bandeau principal avec mois et année -->
            <div class="text-center mb-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    {{ ucfirst($current_month_name) }} {{ $current_year }}
                </h1>
            </div>

            <!-- Navigation des mois -->
            <div class="flex items-center justify-between">
                <button 
                    class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-all duration-200 shadow-lg hover:scale-105" 
                    hx-get="{{ route('calendar.index', ['month' => $prev_month]) }}" 
                    hx-target="#calendar-view" 
                    hx-swap="outerHTML"
                    aria-label="Mois précédent">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <button 
                    class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-all duration-200 shadow-lg hover:scale-105" 
                    hx-get="{{ route('calendar.index', ['month' => $next_month]) }}" 
                    hx-target="#calendar-view" 
                    hx-swap="outerHTML"
                    aria-label="Mois suivant">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Section heropanel -->
    @include('components.heropanel', ['nextSlot' => $nextSlot])

    <!-- Légende des statuts -->
    <div class="px-4 py-3 bg-white border-b border-gray-200">
        <div class="flex flex-wrap items-center justify-center gap-4 text-sm text-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded shadow-sm"></div>
                <span class="font-medium">Inscrit</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-500 rounded shadow-sm"></div>
                <span class="font-medium">Disponible</span>
            </div>
            @if (Auth::user()->role === 'member')
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-gray-400 rounded shadow-sm"></div>
                    <span class="font-medium">Inscriptions closes</span>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-red-500 rounded shadow-sm"></div>
                <span class="font-medium">Annulé</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-gray-300 rounded shadow-sm"></div>
                <span class="font-medium">Terminé</span>
            </div>
        </div>
    </div>

    <!-- En-tête des jours -->
    <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-700 bg-gray-50 px-4 py-3 border-b border-gray-200">
        <div class="text-xs md:text-sm">Lun</div>
        <div class="text-xs md:text-sm">Mar</div>
        <div class="text-xs md:text-sm">Mer</div>
        <div class="text-xs md:text-sm">Jeu</div>
        <div class="text-xs md:text-sm">Ven</div>
        <div class="text-xs md:text-sm">Sam</div>
        <div class="text-xs md:text-sm">Dim</div>
    </div>

    <!-- Grille du calendrier -->
    <div class="grid grid-cols-7 gap-1 p-4">
        @foreach ($calendar_days as $day)
            @php 
                $dayDate = \Carbon\Carbon::parse($day['date']); 
                $isCurrentMonth = ($dayDate->month == $current_month);
                $isToday = $dayDate->isToday();
                $isPast = $dayDate->isPast();
            @endphp
            
            <div class="relative min-h-[80px] md:min-h-[120px] rounded-lg p-2 transition-all duration-200
                        {{ $isCurrentMonth 
                            ? ($isToday 
                                ? 'bg-blue-100 border-2 border-blue-500 text-blue-900 shadow-lg scale-105' 
                                : 'bg-gray-200 text-gray-800 hover:bg-gray-300 border border-gray-400') 
                            : 'bg-gray-300 text-gray-500 border border-gray-400' }} 
                        {{ $isPast && $isCurrentMonth ? 'opacity-60' : '' }}">
                
                <!-- Numéro du jour -->
                <div class="font-bold text-sm md:text-base mb-1 {{ $isToday ? 'text-blue-900' : '' }}">
                    {{ $day['number'] }}
                    @if ($isToday)
                        <span class="ml-1 text-xs">📍</span>
                    @endif
                </div>

                <!-- Événements -->
                <div class="space-y-1">
                    @foreach ($day['events'] as $event)
                        @php
                            if ($event->slot->is_restricted) {
                                $allowedUserIds = $event->slot->whitelist->pluck('user_id');
                                if (!$allowedUserIds->contains(auth()->id())) {
                                    continue;
                                }
                            }
                            $courseDateTime = $event->date->copy()->setTimeFromTimeString($event->slot->start_time);
                            $isPassed = $courseDateTime->isPast();
                            $isRegistered = $event->attendees()->where('user_id', auth()->id())->exists();
                            $isMonitor = $event->monitors()->where('user_id', auth()->id())->exists();
                        @endphp

                        @if ($event->is_cancelled)
                            <!-- Événement annulé - ROUGE -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-2 cursor-pointer transition-all duration-200 hover:bg-red-100 shadow-sm"
                                 hx-get="/calendar/slot/{{ $event->id }}"
                                 hx-target="#modal-body"
                                 hx-swap="innerHTML"
                                 hx-on::after-request="openModal()">
                                <div class="text-xs font-semibold text-red-700 truncate">
                                    {{ $event->slot->name }}
                                </div>
                                <div class="text-xs text-red-600">
                                    {{ \Carbon\Carbon::parse($event->slot->start_time)->format('H:i') }}
                                </div>
                                <div class="text-xs font-bold text-red-700 mt-1">
                                    ❌ Annulé
                                </div>
                            </div>

                        @elseif ($isPassed)
                            <!-- Événement terminé - GRIS -->
                            <div class="bg-gray-100 border border-gray-300 rounded-lg p-2 cursor-pointer transition-all duration-200 hover:bg-gray-200 shadow-sm"
                                 hx-get="/calendar/slot/{{ $event->id }}"
                                 hx-target="#modal-body"
                                 hx-swap="innerHTML"
                                 hx-on::after-request="openModal()">
                                <div class="text-xs font-semibold text-gray-600 truncate">
                                    {{ $event->slot->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($event->slot->start_time)->format('H:i') }}
                                </div>
                                <div class="text-xs font-bold text-gray-600 mt-1">
                                    ✅ Terminé
                                </div>
                            </div>

                        @else
                            <!-- Événement actif -->
                            @php
                                // Vérifier si les inscriptions sont closes (pour les membres uniquement)
                                $isRegistrationClosed = false;
                                $registrationStatus = null;
                                if (Auth::user()->role === 'member') {
                                    $isRegistrationClosed = $event->isRegistrationClosed();
                                    $registrationStatus = $event->getRegistrationStatus();
                                }
                            @endphp
                            
                            <div class="relative rounded-lg p-2 cursor-pointer transition-all duration-200 hover:scale-105 shadow-sm
                                        {{ $isRegistered 
                                            ? 'bg-green-50 border border-green-200 text-green-800 hover:bg-green-100' 
                                            : ($isMonitor 
                                                ? 'bg-blue-50 border border-blue-200 text-blue-800 hover:bg-blue-100' 
                                                : ($isRegistrationClosed 
                                                    ? 'bg-gray-50 border border-gray-300 text-gray-600 hover:bg-gray-100' 
                                                    : 'bg-blue-50 border border-blue-200 text-blue-800 hover:bg-blue-100')) }}"
                                 hx-get="/calendar/slot/{{ $event->id }}"
                                 hx-target="#modal-body"
                                 hx-swap="innerHTML"
                                 hx-on::after-request="openModal()">
                                
                                <!-- Nom du cours -->
                                <div class="text-xs font-semibold truncate mb-1">
                                    {{ $event->slot->name }}
                                </div>
                                
                                <!-- Horaires -->
                                <div class="text-xs opacity-90">
                                    {{ \Carbon\Carbon::parse($event->slot->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($event->slot->end_time)->format('H:i') }}
                                </div>

                                <!-- Statut d'inscription -->
                                <div class="mt-2 text-xs font-bold">
                                    @if ($isRegistered)
                                        ✅ Inscrit
                                    @elseif ($isMonitor)
                                        🎓 Moniteur
                                    @elseif ($isRegistrationClosed)
                                        🔒 Inscriptions closes
                                    @else
                                        📝 Disponible
                                    @endif
                                </div>

                                <!-- Indicateur de clôture pour les membres -->
                                @if (Auth::user()->role === 'member' && $isRegistrationClosed)
                                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-gray-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg">
                                        🔒
                                    </div>
                                @endif

                                <!-- Indicateurs selon le rôle -->
                                @if (Auth::user()->role == 'admin-club')
                                    <!-- Admins : tout voir + possibilité d'annuler -->
                                    <div class="mt-1 text-xs opacity-75">
                                        👥 {{ $event->attendees()->count() }} / 🎓 {{ $event->monitors()->count() }}
                                    </div>
                                    
                                    @php
                                        // Déterminer le statut de risque pour les admins
                                        $isAtRisk = false;
                                        if ($event->slot->capacity_type === 'dynamic') {
                                            $monitorCount = $event->monitors()->count();
                                            $attendeeCount = $event->attendees()->count();
                                            $capacity = $monitorCount * 5;
                                            $isAtRisk = $attendeeCount > $capacity || $monitorCount === 0;
                                        }
                                    @endphp
                                    
                                    @if ($isAtRisk)
                                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg">
                                            ⚠️
                                        </div>
                                    @endif

                                @elseif (Auth::user()->role == 'monitor')
                                    <!-- Moniteurs : tout voir mais pas d'annulation -->
                                    <div class="mt-1 text-xs opacity-75">
                                        👥 {{ $event->attendees()->count() }} / 🎓 {{ $event->monitors()->count() }}
                                    </div>
                                    
                                    @php
                                        // Déterminer le statut de risque pour les moniteurs
                                        $isAtRisk = false;
                                        if ($event->slot->capacity_type === 'dynamic') {
                                            $monitorCount = $event->monitors()->count();
                                            $attendeeCount = $event->attendees()->count();
                                            $capacity = $monitorCount * 5;
                                            $isAtRisk = $attendeeCount > $capacity || $monitorCount === 0;
                                        }
                                    @endphp
                                    
                                    @if ($isAtRisk)
                                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg">
                                            ⚠️
                                        </div>
                                    @endif

                                @else
                                    <!-- Membres : interface simple, pas d'informations techniques -->
                                    <!-- Aucun indicateur affiché pour les membres -->
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bouton retour en haut -->
    <div class="fixed bottom-6 right-6 z-30">
        <button 
            onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="w-12 h-12 bg-blue-500 text-white rounded-full shadow-lg hover:bg-blue-600 transition-all duration-200 hover:scale-110 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </button>
    </div>
</div>

<!-- Modal améliorée -->
<div id="modal-overlay" 
     class="hidden fixed inset-0 z-40 flex items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.5);" 
     onclick="closeModal(event)">
    <div id="modal-content" 
         class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform scale-0 transition-all duration-300 max-h-[90vh] overflow-hidden"
         onclick="event.stopPropagation()">
        
        <!-- En-tête de la modal -->
        <div class="bg-blue-500 text-white p-4 rounded-t-2xl flex items-center justify-between">
            <h3 class="text-lg font-bold">Détails de la session</h3>
            <button 
                class="w-8 h-8 rounded-full bg-white text-blue-500 hover:bg-gray-100 transition-all duration-200 flex items-center justify-center"
                onclick="closeModal()">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Contenu de la modal -->
        <div id="modal-body" class="p-4 overflow-y-auto max-h-[calc(90vh-80px)]"></div>
    </div>
</div>

<!-- Tooltip amélioré -->
<div id="tooltip" class="hidden absolute bg-gray-800 text-white z-50 text-sm rounded-lg px-3 py-2 shadow-lg transition-all duration-300 border border-gray-600"></div>

<script>
    // Fonctions modales améliorées
    window.openModal = function() {
        const overlay = document.getElementById('modal-overlay');
        const modal = document.getElementById('modal-content');

        overlay.classList.remove('hidden');
        modal.classList.remove('scale-0');
        modal.classList.add('scale-100');
        
        // Focus sur la modal pour l'accessibilité
        modal.focus();
    }

    window.closeModal = function(event) {
        if (!event || event.target.id === 'modal-overlay') {
            const overlay = document.getElementById('modal-overlay');
            const modal = document.getElementById('modal-content');
            
            modal.classList.remove('scale-100');
            modal.classList.add('scale-0');
            
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }

    // Fermeture avec la touche Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Tooltip amélioré
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

    // Gestion des événements HTMX
    document.body.addEventListener('htmx:afterSwap', (event) => {
        if (event.detail.target.id === "app") {
            // Re-initialiser les icônes si nécessaire
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });

    // Animation d'entrée pour les événements
    document.addEventListener('DOMContentLoaded', function() {
        const events = document.querySelectorAll('[hx-get*="/calendar/slot/"]');
        events.forEach((event, index) => {
            event.style.opacity = '0';
            event.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                event.style.transition = 'all 0.3s ease-out';
                event.style.opacity = '1';
                event.style.transform = 'translateY(0)';
            }, index * 50);
        });
    });
</script>