@if($nextSlot)
    <div class="bg-[#3AAFA9] text-[#17252A] rounded-xl shadow-lg p-6 mb-2">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-xl md:text-2xl font-bold">🎯 Votre prochaine session</h3>
                <p class="mt-1 text-sm md:text-base">
                    <strong>{{ $nextSlot->slot->name }}</strong> le 
                    <strong>{{ \Carbon\Carbon::parse($nextSlot->date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</strong>
                    de <strong>{{ $nextSlot->slot->start_time }}</strong> à <strong>{{ $nextSlot->slot->end_time }}</strong>
                    @if ($nextSlot->slot->location)
                        à <strong>{{ $nextSlot->slot->location }}</strong>
                    @endif
                </p>
            </div>
        </div>
    </div>
@endif
