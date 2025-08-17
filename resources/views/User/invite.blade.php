<div class="bg-[#17252A]/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-[#3AAFA9]/20">
    <!-- En-tête -->
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-[#3AAFA9] rounded-full flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-[#17252A]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-[#FEFFFF] mb-2">Inviter un nouveau membre</h2>
        <p class="text-[#DEF2F1] text-lg">Envoyez une invitation à rejoindre le club</p>
    </div>

    <!-- Messages d'erreur -->
    @if (isset($error))
        <div class="bg-[#FE4A49]/20 border border-[#FE4A49] text-[#FE4A49] p-4 rounded-xl mb-6 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>
                @if ($error == 'EMAIL_ALREADY_EXISTS')
                    Cette adresse email est déjà utilisée par un membre du club.
                @elseif ($error == 'INVITATION_ALREADY_EXISTS')
                    Une invitation a déjà été envoyée à cette adresse email.
                @elseif ($error == 'INVITATION_ERROR')
                    Une erreur est survenue lors de l'envoi de l'invitation. Veuillez réessayer.
                @else
                    Une erreur est survenue. Veuillez réessayer.
                @endif
            </span>
        </div>
    @endif

    <!-- Formulaire -->
    <form hx-post="/admin-club/members/invite" hx-target="#main-adminclub" hx-swap="innerHTML" class="space-y-6">
        @csrf
        
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
                    value="{{ old('name', isset($user) ? $user->name : '') }}" 
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
                    value="{{ old('firstname', isset($user) ? $user->firstname : '') }}" 
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
                autofocus 
                value="{{ old('email', isset($user) ? $user->email : '') }}" 
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
                <option value="user" {{ old('role', isset($user) ? $user->role : '') == 'user' ? 'selected' : '' }}>
                    Membre
                </option>
                <option value="monitor" {{ old('role', isset($user) ? $user->role : '') == 'monitor' ? 'selected' : '' }}>
                    Moniteur
                </option>
                <option value="admin-club" {{ old('role', isset($user) ? $user->role : '') == 'admin-club' ? 'selected' : '' }}>
                    Administrateur du club
                </option>
            </select>
        </div>

        <!-- Club ID caché -->
        <input type="hidden" name="club_id" value="{{ $idClub }}" />

        <!-- Boutons d'action -->
        <div class="flex flex-col sm:flex-row gap-4 pt-6">
            <button 
                type="button"
                hx-get="/admin-club/members"
                hx-target="#main-adminclub" 
                hx-swap="innerHTML"
                class="flex-1 sm:flex-none px-6 py-3 border-2 border-[#3AAFA9] text-[#3AAFA9] hover:bg-[#3AAFA9] hover:text-[#17252A] transition-all duration-200 rounded-xl font-medium flex items-center justify-center"
            >
                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Annuler
            </button>
            <button 
                type="submit"
                class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-[#3AAFA9] to-[#2B7A78] hover:from-[#2B7A78] hover:to-[#3AAFA9] text-[#17252A] transition-all duration-200 rounded-xl font-medium flex items-center justify-center transform hover:scale-[1.02] shadow-lg"
            >
                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
                Envoyer l'invitation
            </button>
        </div>
    </form>

    <!-- Informations -->
    <div class="mt-8 p-4 bg-[#3AAFA9]/10 border border-[#3AAFA9]/20 rounded-xl">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-[#3AAFA9] mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="text-[#DEF2F1] text-sm">
                <p class="font-medium mb-1">Comment fonctionne l'invitation ?</p>
                <ul class="space-y-1 text-[#DEF2F1]/80">
                    <li>• Un email d'invitation sera envoyé à l'adresse spécifiée</li>
                    <li>• L'utilisateur pourra créer son compte en cliquant sur le lien</li>
                    <li>• Il sera automatiquement associé au club avec le rôle choisi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
