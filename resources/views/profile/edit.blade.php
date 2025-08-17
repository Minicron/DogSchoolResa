<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mon Profil
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Informations personnelles -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-[#2B7A78] px-6 py-4">
                    <h3 class="text-lg font-semibold text-[#DEF2F1] flex items-center">
                        <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                        Informations personnelles
                    </h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Changement de mot de passe -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-[#3AAFA9] px-6 py-4">
                    <h3 class="text-lg font-semibold text-[#17252A] flex items-center">
                        <i data-lucide="lock" class="w-5 h-5 mr-2"></i>
                        Sécurité du compte
                    </h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- <!-- Suppression du compte - DÉSACTIVÉE TEMPORAIREMENT -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-red-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i data-lucide="trash-2" class="w-5 h-5 mr-2"></i>
                        Zone de danger
                    </h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div> --}}
        </div>
    </div>
</x-app-layout>
