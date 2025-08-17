<section>
    <div class="mb-6">
        <h4 class="text-lg font-medium text-gray-900 mb-2">
            Changer mon mot de passe
        </h4>
        <p class="text-sm text-gray-600">
            Assurez-vous que votre compte utilise un mot de passe long et sécurisé pour rester protégé.
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Mot de passe actuel -->
        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-900 mb-1">Mot de passe actuel</label>
            <div class="mt-1 relative">
                <x-text-input 
                    id="update_password_current_password" 
                    name="current_password" 
                    type="password" 
                    class="mt-1 block w-full rounded border-gray-300 focus:border-[#3AAFA9] focus:ring-[#3AAFA9] p-2 pr-10" 
                    autocomplete="current-password" 
                    required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- Nouveau mot de passe -->
        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-900 mb-1">Nouveau mot de passe</label>
            <div class="mt-1 relative">
                <x-text-input 
                    id="update_password_password" 
                    name="password" 
                    type="password" 
                    class="mt-1 block w-full rounded border-gray-300 focus:border-[#3AAFA9] focus:ring-[#3AAFA9] p-2 pr-10" 
                    autocomplete="new-password" 
                    required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <p class="mt-2 text-sm text-gray-500">
                Le mot de passe doit contenir au moins 8 caractères.
            </p>
        </div>

        <!-- Confirmation du nouveau mot de passe -->
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-900 mb-1">Confirmer le nouveau mot de passe</label>
            <div class="mt-1 relative">
                <x-text-input 
                    id="update_password_password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    class="mt-1 block w-full rounded border-gray-300 focus:border-[#3AAFA9] focus:ring-[#3AAFA9] p-2 pr-10" 
                    autocomplete="new-password" 
                    required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Bouton de sauvegarde -->
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#3AAFA9] hover:bg-[#2B7A78] focus:ring-[#2B7A78] border-transparent">
                <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                Mettre à jour le mot de passe
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center text-sm text-green-600"
                >
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                    Mot de passe mis à jour avec succès !
                </div>
            @endif
        </div>
    </form>
</section>
