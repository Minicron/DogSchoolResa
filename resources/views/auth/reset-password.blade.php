<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-[#17252A] via-[#2B7A78] to-[#17252A] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- En-tête du formulaire -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-[#3AAFA9] rounded-full flex items-center justify-center mb-6">
                    <i data-lucide="refresh-cw" class="h-8 w-8 text-[#17252A]"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#FEFFFF] mb-2">
                    Nouveau mot de passe
                </h2>
                <p class="text-[#DEF2F1] text-lg">
                    Choisissez votre nouveau mot de passe
                </p>
                <div class="mt-4">
                    <p class="text-sm text-[#DEF2F1]">
                        Club d'Éducation Canine de Condat-Sur-Vienne
                    </p>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-[#17252A]/80 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-[#3AAFA9]/20">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                                :value="old('email', $request->email)" 
                                required 
                                autofocus 
                                autocomplete="username" 
                                placeholder="votre@email.com"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#FE4A49] text-sm" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Nouveau mot de passe')" class="text-[#DEF2F1] text-sm font-medium mb-2 block" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-[#3AAFA9]"></i>
                            </div>
                            <x-text-input 
                                id="password" 
                                class="block w-full pl-10 pr-10 py-3 bg-[#2B7A78]/50 text-[#DEF2F1] border border-[#3AAFA9]/50 rounded-xl focus:border-[#3AAFA9] focus:ring-2 focus:ring-[#3AAFA9]/20 focus:outline-none transition-all duration-200 placeholder-[#DEF2F1]/50" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="new-password" 
                                placeholder="••••••••"
                            />
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i data-lucide="eye" id="password-toggle" class="h-5 w-5 text-[#3AAFA9] hover:text-[#DEF2F1] transition cursor-pointer"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#FE4A49] text-sm" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-[#DEF2F1] text-sm font-medium mb-2 block" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-[#3AAFA9]"></i>
                            </div>
                            <x-text-input 
                                id="password_confirmation" 
                                class="block w-full pl-10 pr-10 py-3 bg-[#2B7A78]/50 text-[#DEF2F1] border border-[#3AAFA9]/50 rounded-xl focus:border-[#3AAFA9] focus:ring-2 focus:ring-[#3AAFA9]/20 focus:outline-none transition-all duration-200 placeholder-[#DEF2F1]/50" 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                autocomplete="new-password" 
                                placeholder="••••••••"
                            />
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i data-lucide="eye" id="password-confirmation-toggle" class="h-5 w-5 text-[#3AAFA9] hover:text-[#DEF2F1] transition cursor-pointer"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-[#FE4A49] text-sm" />
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-[#17252A] bg-gradient-to-r from-[#3AAFA9] to-[#2B7A78] hover:from-[#2B7A78] hover:to-[#3AAFA9] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3AAFA9] transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i data-lucide="check" class="h-5 w-5 text-[#17252A] group-hover:text-[#17252A] transition"></i>
                            </span>
                            {{ __('Réinitialiser le mot de passe') }}
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
                    Votre mot de passe doit contenir au moins 8 caractères
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const passwordToggle = document.getElementById(fieldId === 'password' ? 'password-toggle' : 'password-confirmation-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                passwordToggle.setAttribute('data-lucide', 'eye');
            }
            
            // Re-initialiser l'icône Lucide
            lucide.createIcons();
        }

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
