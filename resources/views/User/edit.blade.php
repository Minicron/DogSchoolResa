<div class="bg-[#17252A]/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-[#3AAFA9]/20">
    <!-- En-tête -->
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-[#3AAFA9] rounded-full flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-[#17252A]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM4 12v4h4v-4H4z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-[#FEFFFF] mb-2">Modifier le membre</h2>
        <p class="text-[#DEF2F1] text-lg">Modifiez les informations de {{ $member->firstname }} {{ $member->name }}</p>
    </div>

    <!-- Messages d'erreur -->
    @if (isset($error))
        <div class="bg-[#FE4A49]/20 border border-[#FE4A49] text-[#FE4A49] p-4 rounded-xl mb-6 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $error }}</span>
        </div>
    @endif

    <!-- Formulaire -->
    <form hx-post="/admin-club/members/edit/{{ $member->id }}" hx-target="#main-adminclub" hx-swap="innerHTML" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Nom et Prénom -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-[#DEF2F1] text-sm font-medium mb-2">
                    <svg class="h-4 w-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    Nom
                </label>
                <input 
                    id="name" 
                    class="block w-full px-4 py-3 bg-[#2B7A78]/50 text-[#DEF2F1] border border-[#3AAFA9]/50 rounded-xl focus:border-[#3AAFA9] focus:ring-2 focus:ring-[#3AAFA9]/20 focus:outline-none transition-all duration-200 placeholder-[#DEF2F1]/50" 
                    type="text" 
                    name="name" 
                    required 
                    value="{{ old('name', $member->name) }}" 
                    placeholder="Nom de famille"
                />
            </div>
            <div>
                <label for="firstname" class="block text-[#DEF2F1] text-sm font-medium mb-2">
                    <svg class="h-4 w-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    Prénom
                </label>
                <input 
                    id="firstname" 
                    class="block w-full px-4 py-3 bg-[#2B7A78]/50 text-[#DEF2F1] border border-[#3AAFA9]/50 rounded-xl focus:border-[#3AAFA9] focus:ring-2 focus:ring-[#3AAFA9]/20 focus:outline-none transition-all duration-200 placeholder-[#DEF2F1]/50" 
                    type="text" 
                    name="firstname" 
                    required 
                    value="{{ old('firstname', $member->firstname) }}" 
                    placeholder="Prénom"
                />
            </div>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-[#DEF2F1] text-sm font-medium mb-2">
                <svg class="h-4 w-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
                Adresse email
            </label>
            <input 
                id="email" 
                class="block w-full px-4 py-3 bg-[#2B7A78]/50 text-[#DEF2F1] border border-[#3AAFA9]/50 rounded-xl focus:border-[#3AAFA9] focus:ring-2 focus:ring-[#3AAFA9]/20 focus:outline-none transition-all duration-200 placeholder-[#DEF2F1]/50" 
                type="email" 
                name="email" 
                required 
                value="{{ old('email', $member->email) }}" 
                placeholder="exemple@email.com"
            />
        </div>

        <!-- Rôle -->
        <div>
            <label for="role" class="block text-[#DEF2F1] text-sm font-medium mb-2">
                <svg class="h-4 w-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Rôle dans le club
            </label>
            <select 
                id="role" 
                class="block w-full px-4 py-3 bg-[#2B7A78]/50 text-[#DEF2F1] border border-[#3AAFA9]/50 rounded-xl focus:border-[#3AAFA9] focus:ring-2 focus:ring-[#3AAFA9]/20 focus:outline-none transition-all duration-200" 
                name="role" 
                required
            >
                <option value="user" {{ old('role', $member->role) == 'user' ? 'selected' : '' }}>
                    Membre
                </option>
                <option value="monitor" {{ old('role', $member->role) == 'monitor' ? 'selected' : '' }}>
                    Moniteur
                </option>
                <option value="admin-club" {{ old('role', $member->role) == 'admin-club' ? 'selected' : '' }}>
                    Administrateur du club
                </option>
            </select>
        </div>

        <!-- Statut actif -->
        <div>
            <label for="is_active" class="flex items-center">
                <input 
                    id="is_active" 
                    type="checkbox" 
                    name="is_active" 
                    value="1" 
                    {{ old('is_active', $member->is_active) ? 'checked' : '' }}
                    class="rounded bg-[#2B7A78] border-[#3AAFA9] text-[#3AAFA9] shadow-sm focus:ring-[#3AAFA9] focus:ring-offset-[#17252A] transition"
                />
                <span class="ml-2 text-[#DEF2F1] text-sm font-medium">Compte actif</span>
            </label>
        </div>

        <!-- Boutons d'action -->
        <div class="flex flex-col sm:flex-row gap-4 pt-6">
            <button 
                type="submit" 
                class="flex-1 bg-gradient-to-r from-[#3AAFA9] to-[#2B7A78] hover:from-[#2B7A78] hover:to-[#3AAFA9] text-[#17252A] transition-all duration-200 rounded-xl px-6 py-3 font-medium flex items-center justify-center transform hover:scale-[1.02] shadow-lg">
                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Enregistrer les modifications
            </button>
            <button 
                type="button" 
                class="flex-1 bg-[#2B7A78] hover:bg-[#17252A] text-[#DEF2F1] transition-all duration-200 rounded-xl px-6 py-3 font-medium flex items-center justify-center transform hover:scale-[1.02] shadow-lg"
                hx-get="/admin-club/members"
                hx-target="#main-adminclub" 
                hx-swap="innerHTML">
                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Annuler
            </button>
        </div>
    </form>
</div>
