<!-- resources/views/partials/calendar_event_details.blade.php -->
<div class="p-4">
    <h3 class="text-xl font-bold mb-2 text-gray-800">{{ $event->slot->name }}</h3>
    <p class="mb-2 text-gray-600">
        {{ \Carbon\Carbon::parse($event->date)->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}<br>
        Heure : De {{ $event->slot->start_time }} à {{ $event->slot->end_time }}
    </p>
    <p class="mb-4 text-gray-700">{{ $event->slot->description }}</p>
    <!-- Bouton d'inscription (adapté à la logique déjà en place) -->
    <form action="{{ route('slot.register', $event->id) }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="w-full bg-[#2B7A78] hover:bg-[#3AAFA9] text-white font-semibold py-2 rounded-lg transition">
            S'inscrire
        </button>
    </form>
</div>
