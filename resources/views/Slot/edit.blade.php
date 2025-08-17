<div x-data="{ 
    autoClose: {{ old('auto_close', $slot->auto_close) ? 'true' : 'false' }},
    capacityType: '{{ old('capacity_type', $slot->capacity_type ?? 'none') }}',
    showConfirmationModal: false,
    confirmationData: null,
    originalDay: {{ $slot->day_of_week }},
    originalStartTime: '{{ $slot->start_time }}',
    originalEndTime: '{{ $slot->end_time }}',
    
    init() {
        console.log('Alpine.js initialized with:', {
            originalDay: this.originalDay,
            originalStartTime: this.originalStartTime,
            originalEndTime: this.originalEndTime,
            showConfirmationModal: this.showConfirmationModal
        });
    },
    
    async handleSubmit(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const dayChanged = this.originalDay != formData.get('day_of_week');
        const timeChanged = this.originalStartTime != formData.get('time_start') || this.originalEndTime != formData.get('time_end');
        
        console.log('Debug - Original:', { day: this.originalDay, start: this.originalStartTime, end: this.originalEndTime });
        console.log('Debug - New:', { day: formData.get('day_of_week'), start: formData.get('time_start'), end: formData.get('time_end') });
        console.log('Debug - Changed:', { dayChanged, timeChanged });
        
        if (dayChanged || timeChanged) {
            console.log('Debug - Showing confirmation modal');
            this.confirmationData = {
                day: formData.get('day_of_week'),
                start_time: formData.get('time_start'),
                end_time: formData.get('time_end'),
                name: formData.get('name'),
                description: formData.get('description'),
                location: formData.get('location'),
                capacity_type: formData.get('capacity_type'),
                capacity: formData.get('capacity'),
                auto_close: formData.get('auto_close'),
                close_duration: formData.get('close_duration'),
                is_restricted: formData.get('is_restricted'),
                has_groups: formData.get('has_groups'),
                groups: formData.getAll('groups[]')
            };
            this.showConfirmationModal = true;
            console.log('Debug - Modal should be visible:', this.showConfirmationModal);
            console.log('Debug - showConfirmationModal value:', this.showConfirmationModal);
            console.log('Debug - confirmationData:', this.confirmationData);
        } else {
            console.log('Debug - No schedule change, submitting normally');
            // Pas de changement d'horaire, soumission normale
            await this.submitNormalForm(formData);
        }
    },
    
    async submitNormalForm(formData) {
        try {
            const response = await fetch('/admin-club/slots/edit/{{ $slot->id }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: formData
            });
            
            if (response.ok) {
                // Recharger la page des slots
                htmx.ajax('GET', '/admin-club/slots', '#main-adminclub');
            } else {
                alert('Erreur lors de la modification du créneau');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la modification du créneau');
        }
    },
    
    async confirmScheduleChange() {
        this.showConfirmationModal = false;
        
        const formData = new FormData();
        
        // Ajouter les données avec les bons noms de champs
        formData.append('day_of_week', this.confirmationData.day || '');
        formData.append('time_start', this.confirmationData.start_time || '');
        formData.append('time_end', this.confirmationData.end_time || '');
        formData.append('name', this.confirmationData.name || '');
        formData.append('description', this.confirmationData.description || '');
        formData.append('location', this.confirmationData.location || '');
        formData.append('capacity_type', this.confirmationData.capacity_type || '');
        formData.append('capacity', this.confirmationData.capacity || '');
        formData.append('auto_close', this.confirmationData.auto_close || '');
        formData.append('close_duration', this.confirmationData.close_duration || '');
        formData.append('is_restricted', this.confirmationData.is_restricted || '');
        formData.append('has_groups', this.confirmationData.has_groups || '');
        
        // Ajouter les groupes s'ils existent
        if (this.confirmationData.groups && Array.isArray(this.confirmationData.groups)) {
            this.confirmationData.groups.forEach(value => {
                formData.append('groups[]', value);
            });
        }
        
        // Ajouter le token CSRF
        formData.append('_token', document.querySelector('meta[name=csrf-token]').getAttribute('content'));
        
        console.log('Debug - FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        try {
            const response = await fetch('/admin-club/slots/edit-with-schedule-change/{{ $slot->id }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Recharger la page des slots
                htmx.ajax('GET', '/admin-club/slots', '#main-adminclub');
            } else {
                alert('Erreur lors de la modification : ' + result.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la modification du créneau');
        }
    },
    
    cancelScheduleChange() {
        this.showConfirmationModal = false;
        this.confirmationData = null;
    }
}">
    <div class="bg-[#17252A] shadow-md rounded-lg p-6 max-w-3xl mx-auto">
        <h2 class="text-2xl text-[#DEF2F1] font-bold mb-6">Modifier un cours</h2>
        <form @submit="handleSubmit" class="space-y-4">
        @csrf
        <!-- Nom -->
        <div>
            <label for="name" class="block text-sm font-medium text-[#DEF2F1]">Nom</label>
            <input id="name" name="name" type="text" required autofocus 
                   value="{{ old('name', $slot->name) }}"
                   class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] 
                          focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
        </div>

        <!-- Grille: Jour & Lieu -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="day_of_week" class="block text-sm font-medium text-[#DEF2F1]">Jour</label>
                <select id="day_of_week" name="day_of_week" required
                        class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] 
                               focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]">
                    <option value="1" {{ old('day_of_week', $slot->day_of_week) == 1 ? 'selected' : '' }}>Lundi</option>
                    <option value="2" {{ old('day_of_week', $slot->day_of_week) == 2 ? 'selected' : '' }}>Mardi</option>
                    <option value="3" {{ old('day_of_week', $slot->day_of_week) == 3 ? 'selected' : '' }}>Mercredi</option>
                    <option value="4" {{ old('day_of_week', $slot->day_of_week) == 4 ? 'selected' : '' }}>Jeudi</option>
                    <option value="5" {{ old('day_of_week', $slot->day_of_week) == 5 ? 'selected' : '' }}>Vendredi</option>
                    <option value="6" {{ old('day_of_week', $slot->day_of_week) == 6 ? 'selected' : '' }}>Samedi</option>
                    <option value="7" {{ old('day_of_week', $slot->day_of_week) == 7 ? 'selected' : '' }}>Dimanche</option>
                </select>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-[#DEF2F1]">Lieu</label>
                <input id="location" name="location" type="text" required 
                       value="{{ old('location', $slot->location) }}"
                       class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] 
                              focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
            </div>
        </div>

        <!-- Plage horaire : début et fin regroupés -->
        <div>
            <label class="block text-sm font-medium text-[#DEF2F1]">Plage horaire</label>
            <div class="mt-1 flex items-center space-x-2">
                <input id="time_start" name="time_start" type="time" required 
                       value="{{ old('time_start', $slot->start_time) }}"
                       class="w-1/2 rounded bg-[#2B7A78] border border-[#22567d] 
                              focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
                <span class="text-[#DEF2F1] font-bold">à</span>
                <input id="time_end" name="time_end" type="time" required 
                       value="{{ old('time_end', $slot->end_time) }}"
                       class="w-1/2 rounded bg-[#2B7A78] border border-[#22567d] 
                              focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" />
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-[#DEF2F1]">Description</label>
            <textarea id="description" name="description" rows="3" required
                      class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] 
                             focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]">{{ old('description', $slot->description) }}</textarea>
        </div>

        <!-- Gestion de la capacité -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#DEF2F1] mb-2">Gestion de la capacité</label>
                
                <!-- Option 1: Pas de limite -->
                <div class="flex items-center mb-3">
                    <input type="radio" id="capacity_none" name="capacity_type" value="none" x-model="capacityType"
                           {{ old('capacity_type', $slot->capacity_type ?? 'none') == 'none' ? 'checked' : '' }}
                           class="h-4 w-4 text-[#3AAFA9] focus:ring-[#3AAFA9] border-gray-300">
                    <label for="capacity_none" class="ml-2 block text-sm text-[#DEF2F1]">
                        <strong>Aucune limite</strong> - Le cours peut accepter un nombre illimité de participants
                    </label>
                </div>
                
                <!-- Option 2: Limite fixe -->
                <div class="flex items-center mb-3">
                    <input type="radio" id="capacity_fixed" name="capacity_type" value="fixed" x-model="capacityType"
                           {{ old('capacity_type', $slot->capacity_type ?? 'none') == 'fixed' ? 'checked' : '' }}
                           class="h-4 w-4 text-[#3AAFA9] focus:ring-[#3AAFA9] border-gray-300">
                    <label for="capacity_fixed" class="ml-2 block text-sm text-[#DEF2F1]">
                        <strong>Limite fixe</strong> - Définir un nombre maximum de participants
                    </label>
                </div>
                
                <!-- Option 3: Limite dynamique -->
                <div class="flex items-center mb-3">
                    <input type="radio" id="capacity_dynamic" name="capacity_type" value="dynamic" x-model="capacityType"
                           {{ old('capacity_type', $slot->capacity_type ?? 'none') == 'dynamic' ? 'checked' : '' }}
                           class="h-4 w-4 text-[#3AAFA9] focus:ring-[#3AAFA9] border-gray-300">
                    <label for="capacity_dynamic" class="ml-2 block text-sm text-[#DEF2F1]">
                        <strong>Limite dynamique</strong> - La capacité dépend du nombre de moniteurs (× 5)
                    </label>
                </div>
            </div>
            
            <!-- Champs conditionnels -->
            <div x-show="capacityType === 'fixed'" class="ml-6">
                <label for="capacity" class="block text-sm font-medium text-[#DEF2F1]">Nombre maximum de participants</label>
                <input id="capacity" name="capacity" type="number" min="1" x-bind:required="capacityType === 'fixed'"
                       value="{{ old('capacity', $slot->capacity) }}"
                       class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] 
                              focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]" 
                       placeholder="Ex: 20" />
            </div>
            
            <div x-show="capacityType === 'dynamic'" class="ml-6">
                <div class="bg-[#3AAFA9]/20 border border-[#3AAFA9] rounded-lg p-3">
                    <p class="text-sm text-[#DEF2F1]">
                        <strong>Capacité automatique :</strong> Le nombre de places sera calculé automatiquement selon la formule :<br>
                        <span class="font-mono text-[#3AAFA9]">Nombre de moniteurs inscrits × 5</span>
                    </p>
                    <p class="text-xs text-[#DEF2F1]/70 mt-2">
                        La capacité s'ajustera automatiquement à chaque inscription/désinscription de moniteur
                    </p>
                </div>
            </div>
        </div>

        @php
    $hasGroups = old('has_groups') !== null
        ? true
        : ($slot->has_groups ?? false);

    $groupNames = old('groups') ?? ($groups ?? []);
@endphp

<div 
    x-data="{
        hasGroups: {{ $hasGroups ? 'true' : 'false' }},
        groups: {{ json_encode($groupNames) }},
        addGroup() { this.groups.push(''); },
        removeGroup(index) { this.groups.splice(index, 1); }
    }"
    class="space-y-2 mt-4"
>
    <div class="flex items-center">
        <input type="checkbox" id="has_groups" name="has_groups" x-model="hasGroups"
               class="h-4 w-4 rounded text-[#3AAFA9] focus:ring-[#3AAFA9] border-gray-300">
        <label for="has_groups" class="ml-2 block text-sm font-medium text-[#DEF2F1]">
            Ce créneau comporte des sous-groupes
        </label>
    </div>

    <div x-show="hasGroups" x-cloak class="mt-2 space-y-2">
        <template x-for="(group, index) in groups" :key="index">
            <div class="flex gap-2">
                <input type="text" :name="'groups[' + index + ']'" x-model="groups[index]"
                       class="w-full rounded bg-[#2B7A78] border border-[#22567d] 
                              focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]"
                       placeholder="Nom du groupe" />
                <button type="button" @click="removeGroup(index)" 
                        class="text-red-500 hover:text-red-700 text-xl font-bold">&times;</button>
            </div>
        </template>

        <button type="button" @click="addGroup"
                class="text-sm font-semibold text-[#3AAFA9] hover:underline">
            + Ajouter un groupe
        </button>
    </div>
</div>


        <!-- Case à cocher pour limiter l'inscription -->
        <div class="flex items-center">
            <input type="checkbox" id="is_restricted" name="is_restricted"
                   class="h-4 w-4 rounded text-[#3AAFA9] focus:ring-[#3AAFA9] border-gray-300"
                   {{ old('is_restricted', $slot->is_restricted) ? 'checked' : '' }} />
            <label for="is_restricted" class="ml-2 block text-sm font-medium text-[#DEF2F1]">
                Limiter qui peut s'inscrire à ce créneau
            </label>
        </div>

        <!-- Options de clôture -->
        <div class="space-y-2">
            <div class="flex items-center">
                <input id="auto_close" type="checkbox" x-model="autoClose" name="auto_close"
                       class="h-4 w-4 rounded bg-[#2B7A78] border border-[#22567d] focus:ring-[#3AAFA9]"
                       {{ old('auto_close', $slot->auto_close) ? 'checked' : '' }} />
                <label for="auto_close" class="ml-2 block text-sm font-medium text-[#DEF2F1]">
                    Clôture automatique des inscriptions
                </label>
            </div>
            <div x-show="autoClose" x-cloak>
                <label for="close_duration" class="block text-sm font-medium text-[#DEF2F1]">
                    Durée avant clôture (heures)
                </label>
                <select id="close_duration" name="close_duration"
                        class="mt-1 block w-full rounded bg-[#2B7A78] border border-[#22567d] 
                               focus:ring-[#3AAFA9] focus:border-[#3AAFA9] p-2 text-[#DEF2F1]">
                    <option value="72" {{ old('close_duration', $slot->close_duration) == 72 ? 'selected' : '' }}>72</option>
                    <option value="48" {{ old('close_duration', $slot->close_duration) == 48 ? 'selected' : '' }}>48</option>
                    <option value="24" {{ old('close_duration', $slot->close_duration) == 24 ? 'selected' : '' }}>24</option>
                    <option value="12" {{ old('close_duration', $slot->close_duration) == 12 ? 'selected' : '' }}>12</option>
                </select>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end gap-4 mt-6">
            <button 
                type="button"
                hx-get="/admin-club/slots"
                hx-target="#main-adminclub" 
                hx-swap="innerHTML"
                class="bg-[#3AAFA9] hover:bg-[#3AAFA9]/80 text-[#17252A] font-bold py-2 px-6 rounded transition"
            >
                Annuler
            </button>
            <button type="submit" 
                    class="bg-[#3AAFA9] hover:bg-[#3AAFA9]/80 text-[#17252A] font-bold py-2 px-6 rounded transition">
                Modifier
            </button>
        </div>
    </form>

    <!-- Modal de confirmation pour changement d'horaire -->
    <div x-show="showConfirmationModal" x-cloak 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Confirmation requise</h3>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    Vous avez modifié l'horaire de ce créneau. Cette action va :
                </p>
                <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                    <li>Annuler tous les cours futurs de ce créneau</li>
                    <li>Notifier tous les participants inscrits</li>
                    <li>Créer de nouveaux cours avec le nouvel horaire</li>
                    <li>Les participants devront se réinscrire aux nouveaux cours</li>
                </ul>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" @click="cancelScheduleChange"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition">
                    Annuler
                </button>
                <button type="button" @click="confirmScheduleChange"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                    Confirmer la modification
                </button>
            </div>
        </div>
    </div>

    <!-- Debug indicator -->
    <div x-show="showConfirmationModal" class="fixed top-4 right-4 bg-red-500 text-white p-2 rounded z-50">
        Modal should be visible!
    </div>
</div>
