<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Slot;
use App\Models\SlotOccurence;
use Carbon\Carbon;

class GenerateSlotOccurences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-slot-occurences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        // For each Slots, generate SlotOccurences for the next 4 weeks
        $slots = Slot::all();

        foreach ($slots as $slot) {

            // Check the day of the week
            $dayOfWeek = $slot->day_of_week;

            // Get the current day of the week
            $currentDayOfWeek = Carbon::now()->dayOfWeek;

            // Calculate the number of days to add to get to the next slot day
            $daysToAdd = $dayOfWeek - $currentDayOfWeek;

            // If the day of the week is in the past, add 7 days
            if ($daysToAdd < 0) {
                $daysToAdd += 7;
            }

            // Add the days to the current date
            $date = Carbon::now()->addDays($daysToAdd)->format('d-m-Y');

            // Create the SlotOccurences
            for ($i = 0; $i < 8; $i++) {

                // Check if the occurence already exists
                $occurence = SlotOccurence::where('slot_id', $slot->id)
                    ->where('date', $date)
                    ->first();

                if ($occurence) {
                    $daysToAdd += 7;
                    $date = Carbon::now()->addDays($daysToAdd)->format('d-m-Y');
                    continue;
                }

                $slotOccurence = new SlotOccurence();
                $slotOccurence->slot_id = $slot->id;
                $slotOccurence->date = $date;
                $slotOccurence->is_cancelled = false;
                $slotOccurence->is_full = false;
                $slotOccurence->created_at = Carbon::now();
                $slotOccurence->updated_at = Carbon::now();
                $slotOccurence->save();

                $daysToAdd += 7;
                $date = Carbon::now()->addDays($daysToAdd)->format('d-m-Y');
            }
        }

        $this->info('Slot occurences generated');
    }
}
