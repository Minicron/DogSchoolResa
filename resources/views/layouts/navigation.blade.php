<nav x-data="{ open: false }" class="bg-[#17252A] border-b border-[#2B7A78]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <i data-lucide="home" class="block h-9 w-9 text-[#DEF2F1]"></i>
                    </a>
                </div>

                <!-- Navigation Links -->
                @if (Auth::user() && Auth::user()->role == 'super-admin')
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-[#DEF2F1] hover:text-[#3AAFA9]">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link>
                            <a 
                                href="{{ route('super-admin.index') }}" 
                                class="text-[#DEF2F1] hover:text-[#3AAFA9] transition"
                                hx-trigger="click"
                                hx-get="/super-admin"
                                hx-target="#app" 
                                hx-swap="innerHTML"
                            >
                                {{ __('Super Admin') }}
                            </a>
                        </x-nav-link>
                    </div>
                @elseif (Auth::user() && Auth::user()->role == 'admin-club')
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a
                            href="{{ route('admin-club.index') }}"
                            class="text-[#DEF2F1] hover:text-[#3AAFA9] transition inline-flex items-center"
                            hx-trigger="click"
                            hx-get="/admin-club"
                            hx-target="#app" 
                            hx-swap="innerHTML"
                        >
                            {{ __('Admin Club') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Settings Dropdown -->
            @if (Auth::user())
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-[#DEF2F1] bg-[#2B7A78] hover:bg-[#3AAFA9] transition">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil') }}
                            </x-dropdown-link>

                            <!-- Déconnexion -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <x-nav-link>
                        <a href="{{ route('login') }}" class="text-[#DEF2F1] hover:text-[#3AAFA9] transition">
                            {{ __('Se connecter') }}
                        </a>
                    </x-nav-link>
                </div>
            @endif

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-[#DEF2F1] hover:bg-[#2B7A78] transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-[#DEF2F1] hover:text-[#3AAFA9]">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @if (Auth::user())
            <div class="pt-4 pb-1 border-t border-[#2B7A78]">
                <div class="px-4">
                    <div class="font-medium text-base text-[#DEF2F1]">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-[#3AAFA9]">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profil') }}
                    </x-responsive-nav-link>

                    <!-- Déconnexion -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Déconnexion') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endif
    </div>
</nav>
