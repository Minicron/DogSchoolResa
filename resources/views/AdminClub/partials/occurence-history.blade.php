<div class="bg-[#FEFFFF] p-4 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold text-[#17252A] mb-3">Historique de l’occurrence</h2>

    @if($occurence->histories->isEmpty())
        <p class="text-center text-gray-500">Aucune action enregistrée.</p>
    @else
        <div class="overflow-y-auto max-h-96">
            <ul class="divide-y divide-gray-300">
                @foreach($occurence->histories as $history)
                    <li class="py-2 flex items-center justify-between">
                        <!-- Date & Heure -->
                        <div class="text-sm text-gray-600 flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                            {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}
                        </div>

                        <!-- Action -->
                        <span class="text-sm font-medium text-[#2B7A78]">
                            {{ $history->action }}
                        </span>

                        <!-- Utilisateur -->
                        <span class="text-sm text-gray-700">
                            <i data-lucide="user" class="w-4 h-4 mr-1 inline"></i>
                            {{ $history->user->firstname }} {{ $history->user->name }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
