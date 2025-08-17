<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-[#17252A] via-[#2B7A78] to-[#17252A] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- En-tête du formulaire -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-[#3AAFA9] rounded-full flex items-center justify-center mb-6">
                    <i data-lucide="key" class="h-8 w-8 text-[#17252A]"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#FEFFFF] mb-2">
                    Mot de passe oublié
                </h2>
                <p class="text-[#DEF2F1] text-lg">
                    Nous vous enverrons un lien de réinitialisation
                </p>
                <div class="mt-4">
                    <p class="text-sm text-[#DEF2F1]">
                        Club d'Éducation Canine de Condat-Sur-Vienne
                    </p>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-[#17252A]/80 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-[#3AAFA9]/20">
                <div class="mb-6 text-sm text-[#DEF2F1] bg-[#3AAFA9]/20 p-4 rounded-lg">
                    {{ __('Mot de passe oublié ? Pas de problème. Indiquez-nous votre adresse email et nous vous enverrons un lien de réinitialisation qui vous permettra de choisir un nouveau mot de passe.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6 text-[#DEF2F1] bg-[#3AAFA9]/20 p-3 rounded-lg" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Adresse email')" class="text-[#DEF2F1] text-sm font-medium mb-2 block" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="h-5 w-5 text-[#3AAFA9]"></i>
                            </div>
                            <x-text-input 
                                id="email" 
                                class="block w-full pl-10 pr-3 py-3 bg-[#2B7A78]/50 text-[#DEF2F1] border border-[#3AAFA9]/50 rounded-xl focus:border-[#3AAFA9] focus:ring-2 focus:ring-[#3AAFA9]/20 focus:outline-none transition-all duration-200 placeholder-[#DEF2F1]/50" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                required 
                                autofocus 
                                placeholder="votre@email.com"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#FE4A49] text-sm" />
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-[#17252A] bg-gradient-to-r from-[#3AAFA9] to-[#2B7A78] hover:from-[#2B7A78] hover:to-[#3AAFA9] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3AAFA9] transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i data-lucide="send" class="h-5 w-5 text-[#17252A] group-hover:text-[#17252A] transition"></i>
                            </span>
                            {{ __('Envoyer le lien de réinitialisation') }}
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-[#3AAFA9]/30"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-[#17252A]/80 text-[#DEF2F1]">Retour à la connexion</span>
                        </div>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center text-[#3AAFA9] hover:text-[#DEF2F1] transition group">
                        <i data-lucide="arrow-left" class="h-4 w-4 mr-2 group-hover:scale-110 transition"></i>
                        {{ __('Retour à la connexion') }}
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-[#DEF2F1]/60">
                    Le lien sera envoyé à l'adresse email associée à votre compte
                </p>
            </div>
        </div>
    </div>

    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.max-w-md');
            form.style.opacity = '0';
            form.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                form.style.transition = 'all 0.6s ease-out';
                form.style.opacity = '1';
                form.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</x-app-layout>
