<x-app-layout>
    @if (Auth::user())
        <div class="py-12">        
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Section header -->
                <div class="bg-[#DEF2F1] p-6 rounded-lg shadow-lg mb-6">
                    <h3 class="text-xl font-semibold text-[#17252A]">Prochains rendez-vous</h3>
                </div>

                <!-- Grid layout for the schedule slots -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
                    @foreach ($slotOccurences->sortBy(function($slotOccurence) {
                        return \Carbon\Carbon::createFromFormat('Y-m-d', $slotOccurence->date);
                    }) as $slotOccurence)
                        @php
                            // Filtrer les slots restreints : si le slot est limité et que l'utilisateur n'est pas dans la whitelist, passer à la prochaine occurrence
                            if ($slotOccurence->slot->is_restricted) {
                                $allowedUserIds = $slotOccurence->slot->whitelist->pluck('user_id');
                                if (!$allowedUserIds->contains(auth()->id())) {
                                    continue;
                                }
                            }

                            // Vérifier l'inscription pour les membres et pour les moniteurs
                            $isRegisteredAsMember = $slotOccurence->attendees()->where('user_id', auth()->id())->exists();
                            $isRegisteredAsMonitor = $slotOccurence->monitors()->where('user_id', auth()->id())->exists();

                            // Reconstituer la date et l'heure du cours (pour les membres)
                            $courseDateTime = \Carbon\Carbon::parse($slotOccurence->date . ' ' . $slotOccurence->slot->start_time);

                            // Vérifier si l'inscription doit être fermée (pour les membres)
                            $registrationClosed = false;
                            if ($slotOccurence->slot->auto_close && !is_null($slotOccurence->slot->close_duration)) {
                                $deadline = \Carbon\Carbon::now()->addHours($slotOccurence->slot->close_duration);
                                if ($deadline->greaterThan($courseDateTime)) {
                                    $registrationClosed = true;
                                }
                            }
                        @endphp

                        <div id="slot-{{ $slotOccurence->id }}" class="bg-[#FEFFFF] text-[#17252A] p-6 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-semibold">
                                    {{ \Carbon\Carbon::parse($slotOccurence->date)->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}
                                </h4>
                                <span class="text-xs bg-[#DEF2F1] text-[#17252A] rounded-full px-2 py-1">
                                    {{ $slotOccurence->slot->name }}
                                </span>
                            </div>

                            @if ($slotOccurences->isNotEmpty() && $slotOccurence->is_cancelled)
                                <!-- Occurrence annulée -->
                                <p class="text-red-600 font-medium">
                                    ❌ Annulé : {{ $slotOccurence->cancellation->reason }}
                                </p>
                            @else
                                @if ($slotOccurence->is_full)
                                    <p class="text-red-600 font-medium">Ce créneau est complet</p>
                                @elseif ($registrationClosed)
                                    <div class="p-4 bg-gray-200 rounded-lg text-center">
                                        <p class="text-red-600 font-bold">Inscription terminée</p>
                                        @if ($isRegisteredAsMember)
                                            <p class="text-green-600 font-semibold mt-2">Vous êtes inscrit(e) en tant que membre</p>
                                        @else
                                            <p class="text-gray-600 font-semibold mt-2">Vous ne vous êtes pas inscrit(e)</p>
                                        @endif
                                        <!-- Pour les moniteurs, le bouton reste affiché -->
                                        @if ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && !$isRegisteredAsMonitor)
                                            <form action="{{ route('slot.register.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                                @csrf
                                                <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                                    S'inscrire en tant que moniteur
                                                </button>
                                            </form>
                                        @elseif ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && $isRegisteredAsMonitor)
                                            <form action="{{ route('slot.unregister.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                                    Se désinscrire en tant que moniteur
                                                </button>
                                            </form>
                                            <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                                                Inscrit en tant que moniteur
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-sm text-gray-600">
                                        Heure : De {{ $slotOccurence->slot->start_time }} à {{ $slotOccurence->slot->end_time }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-600">
                                        Places disponibles : {{ $slotOccurence->slot->capacity - $slotOccurence->attendees()->count() }}
                                    </p>

                                    <!-- Bouton d'inscription en tant que membre -->
                                    @if (!$isRegisteredAsMember && !$isRegisteredAsMonitor)
                                        <form action="{{ route('slot.register', $slotOccurence->id) }}" method="POST" class="mt-4">
                                            @csrf
                                            <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                                S'inscrire en tant que membre
                                            </button>
                                        </form>
                                    @elseif ($isRegisteredAsMember)
                                        <form action="{{ route('slot.unregister', $slotOccurence->id) }}" method="POST" class="mt-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                                Se désinscrire en tant que membre
                                            </button>
                                        </form>
                                        <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                                            Inscrit en tant que membre
                                        </div>
                                    @endif

                                    <!-- Bouton d'inscription en tant que moniteur -->
                                    @if ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && !$isRegisteredAsMonitor)
                                        <form action="{{ route('slot.register.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                            @csrf
                                            <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                                S'inscrire en tant que moniteur
                                            </button>
                                        </form>
                                    @elseif ((Auth::user()->role == 'monitor' || Auth::user()->role == 'admin-club') && $isRegisteredAsMonitor)
                                        <form action="{{ route('slot.unregister.monitor', $slotOccurence->id) }}" method="POST" class="mt-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full bg-[#17252A] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
                                                Se désinscrire en tant que moniteur
                                            </button>
                                        </form>
                                        <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg">
                                            Inscrit en tant que moniteur
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>        
        </div>
    @else 
        <!-- Vue pour les non-connectés -->
        <div class="relative bg-[#17252A] min-h-screen flex flex-col items-center justify-center text-white text-center px-6">
            <!-- Hero Section -->
            <div class="max-w-4xl">
                <h1 class="text-5xl font-bold mb-4">Bienvenue sur Notre Plateforme de Réservation</h1>
                <p class="text-lg text-[#DEF2F1] mb-6">
                    Réservez facilement vos créneaux et suivez votre planning en un clic.
                    Une gestion simplifiée et intuitive, accessible à tout moment.
                </p>
                <a href="{{ route('register') }}" class="bg-[#3AAFA9] hover:bg-[#2B7A78] text-white font-bold py-3 px-6 rounded-lg text-lg transition">
                    Demander un compte
                </a>
                <a href="{{ route('login') }}" class="ml-4 border border-white text-white font-bold py-3 px-6 rounded-lg text-lg transition hover:bg-white hover:text-[#17252A]">
                    Se connecter
                </a>
            </div>

            <!-- Features Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16 max-w-6xl">
                <div class="bg-[#2B7A78] p-6 rounded-lg shadow-lg text-center">
                    <h3 class="text-xl font-semibold mb-2">Réservation rapide</h3>
                    <p class="text-[#DEF2F1]">Choisissez et réservez votre créneau en quelques secondes.</p>
                </div>
                <div class="bg-[#3AAFA9] p-6 rounded-lg shadow-lg text-center">
                    <h3 class="text-xl font-semibold mb-2">Suivi en temps réel</h3>
                    <p class="text-[#DEF2F1]">Gardez un œil sur vos sessions et consultez vos inscriptions.</p>
                </div>
                <div class="bg-[#DEF2F1] text-[#17252A] p-6 rounded-lg shadow-lg text-center">
                    <h3 class="text-xl font-semibold mb-2">Expérience fluide</h3>
                    <p>Interface moderne et intuitive pour une gestion simplifiée.</p>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="absolute bottom-4 text-sm text-[#DEF2F1]">
                © 2025 - Tous droits réservés
            </div>
        </div>
    @endif
</x-app-layout>
