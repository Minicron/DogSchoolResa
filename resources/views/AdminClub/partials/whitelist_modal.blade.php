<div class="bg-white rounded-lg shadow-lg w-11/12 max-w-3xl p-6 relative" 
     id="whitelist-modal"
     x-data="{ searchAuthorized: '', searchAvailable: '' }" 
     onclick="event.stopPropagation()">

    <!-- Bouton de fermeture -->
    <button onclick="document.getElementById('whitelist-modal-container').classList.add('hidden')" 
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold">
        &times;
    </button>

    <h3 class="text-xl font-bold mb-4 text-[#17252A]">Gérer l'accès : {{ $slot->name }}</h3>

    <!-- Export CSV -->
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.club.slots.whitelist.export', $slot->id) }}" 
           class="bg-[#3AAFA9] hover:bg-[#2B7A78] text-white py-1 px-3 rounded transition" download>
            Exporter CSV
        </a>
    </div>

    <!-- Section 1 : Membres autorisés -->
    <div class="mb-6">
        <h4 class="text-lg font-semibold text-[#17252A] mb-2">Membres autorisés</h4>
        <input type="text" x-model="searchAuthorized" placeholder="Rechercher dans la liste autorisée" 
               class="w-full p-2 mb-4 border rounded bg-gray-100 focus:outline-none" />
        <div class="overflow-auto max-h-64">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-3 text-left">Nom</th>
                        <th class="py-2 px-3 text-left">Prénom</th>
                        <th class="py-2 px-3 text-left">Email</th>
                        <th class="py-2 px-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($slot->whitelist as $item)
                        @php $user = $item->user; @endphp
                        <tr class="border-b"
                            x-show="searchAuthorized === '' || '{{ strtolower($user->name.' '.$user->firstname.' '.$user->email) }}'.includes(searchAuthorized.toLowerCase())">
                            <td class="py-2 px-3">{{ $user->name }}</td>
                            <td class="py-2 px-3">{{ $user->firstname }}</td>
                            <td class="py-2 px-3">{{ $user->email }}</td>
                            <td class="py-2 px-3">
                                <!-- Bouton Retirer : cible la modale entière -->
                                <button class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded transition"
                                    hx-get="/admin-club/slots/{{ $slot->id }}/whitelist/remove/{{ $user->id }}"
                                    hx-target="#whitelist-modal-container"
                                    hx-swap="innerHTML">
                                    Retirer
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-2 px-3 text-center text-gray-600">
                                Aucun membre autorisé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2 : Membres disponibles -->
    <div>
        <h4 class="text-lg font-semibold text-[#17252A] mb-2">Membres disponibles</h4>
        <input type="text" x-model="searchAvailable" placeholder="Rechercher dans la liste disponible" 
               class="w-full p-2 mb-4 border rounded bg-gray-100 focus:outline-none" />
        <div class="overflow-auto max-h-64">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-3 text-left">Nom</th>
                        <th class="py-2 px-3 text-left">Prénom</th>
                        <th class="py-2 px-3 text-left">Email</th>
                        <th class="py-2 px-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $availableMembers = $members->reject(function($member) use ($slot) {
                            return $slot->whitelist->pluck('user_id')->contains($member->id);
                        });
                    @endphp
                    @forelse ($availableMembers as $member)
                        <tr class="border-b"
                            x-show="searchAvailable === '' || '{{ strtolower($member->name.' '.$member->firstname.' '.$member->email) }}'.includes(searchAvailable.toLowerCase())">
                            <td class="py-2 px-3">{{ $member->name }}</td>
                            <td class="py-2 px-3">{{ $member->firstname }}</td>
                            <td class="py-2 px-3">{{ $member->email }}</td>
                            <td class="py-2 px-3">
                                <!-- Bouton Autoriser : cible la modale entière -->
                                <button class="bg-green-500 hover:bg-green-600 text-white py-1 px-2 rounded transition"
                                    hx-get="/admin-club/slots/{{ $slot->id }}/whitelist/add/{{ $member->id }}"
                                    hx-target="#whitelist-modal-container"
                                    hx-swap="innerHTML">
                                    Autoriser
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-2 px-3 text-center text-gray-600">
                                Aucun membre disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Dès que la modal est injectée, on retire la classe 'hidden' pour l'afficher
    document.getElementById('whitelist-modal-container').classList.remove('hidden');
</script>
