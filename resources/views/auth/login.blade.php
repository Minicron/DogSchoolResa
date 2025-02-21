<x-app-layout>
    <div class="max-w-md mx-auto mt-10 bg-[#17252A] p-8 rounded-lg shadow-lg">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-[#DEF2F1]" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-[#DEF2F1]" />
                <x-text-input id="email" class="block mt-1 w-full bg-[#2B7A78] text-[#DEF2F1] border border-[#3AAFA9] focus:border-[#DEF2F1] focus:ring-[#DEF2F1]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#FE4A49]" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Mot de passe')" class="text-[#DEF2F1]" />
                <x-text-input id="password" class="block mt-1 w-full bg-[#2B7A78] text-[#DEF2F1] border border-[#3AAFA9] focus:border-[#DEF2F1] focus:ring-[#DEF2F1]" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#FE4A49]" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded bg-[#3AAFA9] border-[#DEF2F1] text-[#17252A] shadow-sm focus:ring-[#DEF2F1]" name="remember">
                    <span class="ml-2 text-sm text-[#DEF2F1]">{{ __('Se souvenir de moi') }}</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-[#3AAFA9] hover:text-[#DEF2F1] transition" href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif

                <x-primary-button class="bg-[#3AAFA9] text-[#17252A] px-4 py-2 rounded-md hover:bg-[#DEF2F1] hover:text-[#17252A] transition">
                    {{ __('Connexion') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
