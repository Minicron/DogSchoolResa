@foreach ($nextOccurrences as $occurrence)
    <div class="bg-[#DEF2F1] text-[#17252A] p-4 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg relative">
        <div class="flex justify-between items-center">
            <h4 class="text-lg font-semibold">
                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $occurrence->date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </h4>
            <p class="text-sm text-gray-700 flex items-center">
                <i data-lucide="clock" class="w-4 h-4 mr-1"></i>{{ $occurrence->slot->start_time }}
            </p>
        </div>

        <div class="flex justify-between items-center mt-2 text-sm">
            <p class="font-medium">👥 Participants: {{ $occurrence->attendees()->count() }}</p>
            <span class="underline cursor-pointer text-[#2B7A78] font-bold"
                  onmouseover="showMonitorsTooltip(event, {{ json_encode($occurrence->monitors->map(fn($m) => $m->user->firstname . ' ' . $m->user->name)->toArray(), JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                  onmouseout="hideMonitorsTooltip()">
                🎓 Moniteurs : {{ $occurrence->monitors()->count() }}
            </span>
        </div>
    </div>
@endforeach