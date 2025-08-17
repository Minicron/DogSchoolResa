<section>
    <div class="mb-6">
        <h4 class="text-lg font-medium text-gray-900 mb-2">
            Modifier mes informations
        </h4>
        <p class="text-sm text-gray-600">
            Mettez à jour vos informations personnelles. L'adresse email ne peut pas être modifiée pour des raisons de sécurité.
        </p>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Prénom -->
        <div>
            <label for="firstname" class="block text-sm font-medium text-gray-900 mb-1">Prénom</label>
            <x-text-input 
                id="firstname" 
                name="firstname" 
                type="text" 
                class="mt-1 block w-full rounded border-gray-300 focus:border-[#3AAFA9] focus:ring-[#3AAFA9] p-2" 
                :value="old('firstname', $user->firstname)" 
                required 
                autocomplete="given-name" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('firstname')" />
        </div>

        <!-- Nom -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-900 mb-1">Nom</label>
            <x-text-input 
                id="name" 
                name="name" 
                type="text" 
                class="mt-1 block w-full rounded border-gray-300 focus:border-[#3AAFA9] focus:ring-[#3AAFA9] p-2" 
                :value="old('name', $user->name)" 
                required 
                autocomplete="family-name" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email (lecture seule) -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-900 mb-1">Adresse email</label>
            <div class="mt-1 relative">
                <x-text-input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="block w-full rounded bg-gray-50 border-gray-300 text-gray-500 cursor-not-allowed p-2" 
                    :value="$user->email" 
                    disabled 
                    readonly
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500">
                L'adresse email ne peut pas être modifiée. Contactez l'administrateur si nécessaire.
            </p>
        </div>

        <!-- Bouton de sauvegarde -->
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#2B7A78] hover:bg-[#3AAFA9] focus:ring-[#3AAFA9] border-transparent">
                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                Enregistrer les modifications
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center text-sm text-green-600"
                >
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                    Modifications enregistrées avec succès !
                </div>
            @endif
        </div>
    </form>
</section>
