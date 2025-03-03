<x-app-layout>
    @if (Auth::user())
        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Toggle de vue, placé en haut à droite -->
            <div class="flex justify-end mb-4">
                <form hx-post="{{ route('user.toggle_view') }}" hx-target="#app" hx-swap="innerHTML" class="inline">
                    @csrf
                    <!-- La valeur envoyée est l'inverse de la préférence actuelle -->
                    <input type="hidden" name="calendar_view" value="{{ Auth::user()->calendar_view ? 0 : 1 }}">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded transition hover:bg-blue-600">
                        Basculer en vue {{ Auth::user()->calendar_view ? 'Standard' : 'Calendrier' }}
                    </button>
                </form>
            </div>
            <!-- Contenu principal à remplacer dynamiquement -->
            <div id="main-content">
                @if (Auth::user()->calendar_view)
                    @include('calendar_view')
                @else
                    @include('standard_view')
                @endif
            </div>
        </div>
    @else
        @include('visitor')
    @endif
</x-app-layout>
