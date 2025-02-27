<nav x-data="{ open: false }" class="bg-[#17252A] shadow-lg fixed w-full z-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo et liens principaux -->
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <i data-lucide="home" class="h-10 w-10 text-[#DEF2F1]"></i>
                </a>
                <!-- Liens de navigation -->
                <div class="hidden md:flex ml-10 space-x-6">
                    @if (Auth::check() && Auth::user()->role == 'super-admin')
                        <a href="{{ route('dashboard') }}" class="text-[#DEF2F1] hover:text-[#3AAFA9] transition">
                            Dashboard
                        </a>
                        <a href="{{ route('super-admin.index') }}" 
                           class="text-[#DEF2F1] hover:text-[#3AAFA9] transition"
                           hx-trigger="click"
                           hx-get="/super-admin"
                           hx-target="#app" 
                           hx-swap="innerHTML">
                           Super Admin
                        </a>
                    @elseif (Auth::check() && Auth::user()->role == 'admin-club')
                        <a href="{{ route('admin-club.index') }}" 
                           class="text-[#DEF2F1] hover:text-[#3AAFA9] transition inline-flex items-center"
                           hx-trigger="click"
                           hx-get="/admin-club"
                           hx-target="#app" 
                           hx-swap="innerHTML">
                           Admin Club
                        </a>
                    @else
                        <a href="{{ route('club.index') }}" class="text-[#DEF2F1] hover:text-[#3AAFA9] transition">
                            Clubs
                        </a>
                    @endif
                </div>
            </div>

            <!-- Profil / Authentification et menu mobile -->
            <div class="flex items-center">
                @if (Auth::check())
                    <!-- Dropdown Profil -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" class="flex items-center focus:outline-none focus:ring-2 focus:ring-[#3AAFA9] transition">
                            <span class="text-[#DEF2F1] mr-2">{{ Auth::user()->firstname }}</span>
                            <i data-lucide="user" class="h-8 w-8 text-[#DEF2F1]"></i>
                        </button>
                        <div x-show="profileOpen" @click.away="profileOpen = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-[#FEFFFF] ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#2B7A78] hover:text-[#FEFFFF]">
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-[#2B7A78] hover:text-[#FEFFFF]">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-[#DEF2F1] hover:text-[#3AAFA9] transition">
                        Se connecter
                    </a>
                @endif
                <!-- Bouton hamburger pour mobile -->
                <div class="md:hidden ml-4">
                    <button @click="open = ! open" class="text-[#DEF2F1] hover:text-[#3AAFA9] focus:outline-none focus:ring-2 focus:ring-[#3AAFA9]">
                        <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu mobile -->
    <div x-show="open" class="md:hidden bg-[#17252A]">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @if (Auth::check() && Auth::user()->role == 'super-admin')
                <a href="{{ route('dashboard') }}" class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition px-3 py-2 rounded-md">
                    Dashboard
                </a>
                <a href="{{ route('super-admin.index') }}" 
                   class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition px-3 py-2 rounded-md"
                   hx-trigger="click"
                   hx-get="/super-admin"
                   hx-target="#app" 
                   hx-swap="innerHTML">
                   Super Admin
                </a>
            @elseif (Auth::check() && Auth::user()->role == 'admin-club')
                <a href="{{ route('admin-club.index') }}" 
                   class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition px-3 py-2 rounded-md"
                   hx-trigger="click"
                   hx-get="/admin-club"
                   hx-target="#app" 
                   hx-swap="innerHTML">
                   Admin Club
                </a>
            @else
                <a href="{{ route('club.index') }}" class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition px-3 py-2 rounded-md">
                    Clubs
                </a>
            @endif
            @if (Auth::check())
                <a href="{{ route('profile.edit') }}" class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition px-3 py-2 rounded-md">
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block text-[#DEF2F1] hover:text-[#3AAFA9] transition px-3 py-2 rounded-md">
                        Déconnexion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block text-[#DEF2F1] hover:text-[#3AAFA9] transition px-3 py-2 rounded-md">
                    Se connecter
                </a>
            @endif
        </div>
    </div>
</nav>