<div class="bg-white rounded-lg shadow-lg w-11/12 max-w-xl p-6 relative" 
     id="participants-modal" 
     onclick="event.stopPropagation()">
    <!-- Bouton de fermeture -->
    <button onclick="document.getElementById('participants-modal-container').classList.add('hidden')" 
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold">
        &times;
    </button>
    <h3 class="text-xl font-bold mb-4 text-[#17252A]">Liste des participants</h3>

    <!-- Bouton d'export CSV (en haut) -->
    <div class="flex justify-end mb-2">
        <a href="{{ route('admin.club.slots.participants.export', $slotOccurence->id) }}" 
           class="bg-[#3AAFA9] hover:bg-[#2B7A78] text-white py-1 px-3 rounded transition" download>
            Exporter CSV
        </a>
    </div>

    <div class="overflow-auto max-h-80">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-3 text-left">Nom</th>
                    <th class="py-2 px-3 text-left">Prénom</th>
                    <th class="py-2 px-3 text-left">Email</th>
                    <th class="py-2 px-3 text-left">Date inscription</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($participants as $participant)
                <tr class="border-b">
                    <td class="py-2 px-3">{{ $participant->user->name }}</td>
                    <td class="py-2 px-3">{{ $participant->user->firstname }}</td>
                    <td class="py-2 px-3">{{ $participant->user->email }}</td>
                    <td class="py-2 px-3">
                        {{ \Carbon\Carbon::parse($participant->created_at)->locale('fr')->isoFormat('D MMMM YYYY, HH:mm') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bouton d'export CSV (en bas) -->
    <div class="flex justify-end mt-4">
        <a href="{{ route('admin.club.slots.participants.export', $slotOccurence->id) }}" 
           class="bg-[#3AAFA9] hover:bg-[#2B7A78] text-white py-1 px-3 rounded transition" download>
            Exporter CSV
        </a>
    </div>
</div>

<script>
    // Dès que la modal est chargée, on retire la classe "hidden" du conteneur
    document.getElementById('participants-modal-container').classList.remove('hidden');
</script>
