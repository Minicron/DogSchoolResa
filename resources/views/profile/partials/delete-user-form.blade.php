<section>
    <div class="mb-6">
        <h4 class="text-lg font-medium text-gray-900 mb-2">
            Supprimer mon compte
        </h4>
        <p class="text-sm text-gray-600">
            Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
        </p>
    </div>

    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h5 class="text-sm font-medium text-red-800">
                    Attention : Action irréversible
                </h5>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Toutes vos données seront définitivement supprimées</li>
                        <li>Vos inscriptions aux cours seront annulées</li>
                        <li>Votre historique sera perdu</li>
                        <li>Cette action ne peut pas être annulée</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
        @csrf
        @method('delete')

        <div>
            <x-input-label for="password" value="Confirmer avec votre mot de passe" />
            <div class="mt-1 relative">
                <x-text-input 
                    id="password" 
                    name="password" 
                    type="password" 
                    class="mt-1 block w-full pr-10" 
                    autocomplete="current-password" 
                    required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            <p class="mt-2 text-sm text-gray-500">
                Saisissez votre mot de passe actuel pour confirmer la suppression.
            </p>
        </div>

        <div class="flex items-center gap-4">
            <x-danger-button class="bg-red-600 hover:bg-red-700 focus:ring-red-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Supprimer définitivement mon compte
            </x-danger-button>
        </div>
    </form>
</section>
