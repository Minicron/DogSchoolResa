<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-[#17252A] via-[#2B7A78] to-[#17252A] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- En-tête du formulaire -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-[#3AAFA9] rounded-full flex items-center justify-center mb-6">
                    <i data-lucide="shield-check" class="h-8 w-8 text-[#17252A]"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#FEFFFF] mb-2">
                    Connexion
                </h2>
                <p class="text-[#DEF2F1] text-lg">
                    Accédez à votre espace personnel
                </p>
                <div class="mt-4">
                    <p class="text-sm text-[#DEF2F1]">
                        Club d'Éducation Canine de Condat-Sur-Vienne
                    </p>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-[#17252A]/80 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-[#3AAFA9]/20">
                <!-- Session Status -->
                <x-auth-session-status class="mb-6 text-[#DEF2F1] bg-[#3AAFA9]/20 p-3 rounded-lg" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
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
                                autocomplete="username" 
                                placeholder="votre@email.com"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#FE4A49] text-sm" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Mot de passe')" class="text-[#DEF2F1] text-sm font-medium mb-2 block" />
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
                                autocomplete="current-password" 
                                placeholder="••••••••"
                            />
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="password-toggle" class="h-5 w-5 text-[#3AAFA9] hover:text-[#DEF2F1] transition cursor-pointer" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#FE4A49] text-sm" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                class="rounded bg-[#2B7A78] border-[#3AAFA9] text-[#3AAFA9] shadow-sm focus:ring-[#3AAFA9] focus:ring-offset-[#17252A] transition" 
                                name="remember"
                            >
                            <span class="ml-2 text-sm text-[#DEF2F1]">{{ __('Se souvenir de moi') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#3AAFA9] hover:text-[#DEF2F1] transition underline" href="{{ route('password.request') }}">
                                {{ __('Mot de passe oublié ?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-[#17252A] bg-gradient-to-r from-[#3AAFA9] to-[#2B7A78] hover:from-[#2B7A78] hover:to-[#3AAFA9] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3AAFA9] transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-[#17252A] group-hover:text-[#17252A] transition" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            {{ __('Se connecter') }}
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
                            <span class="px-2 bg-[#17252A]/80 text-[#DEF2F1]">Accès par invitation uniquement</span>
                        </div>
                    </div>
                </div>

                <!-- Info Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-[#DEF2F1]/80">
                        Pour rejoindre le club, contactez un administrateur pour recevoir une invitation.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-[#DEF2F1]/60">
                    En vous connectant, vous acceptez nos conditions d'utilisation
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.innerHTML = '<path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />';
            } else {
                passwordInput.type = 'password';
                passwordToggle.innerHTML = '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />';
            }
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
