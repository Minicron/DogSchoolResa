<?php

namespace App\Services;

use App\Models\SlotOccurence;

class MonitorCapacityService
{
    /**
     * Calcule la capacité actuelle d'une occurrence
     */
    public function getCurrentCapacity(SlotOccurence $slotOccurrence)
    {
        return $slotOccurrence->slot->getCurrentCapacity($slotOccurrence);
    }

    /**
     * Vérifie si une occurrence peut accepter plus de participants
     */
    public function canAcceptMoreParticipants(SlotOccurence $slotOccurrence)
    {
        $currentCapacity = $this->getCurrentCapacity($slotOccurrence);
        
        // Si pas de limite de capacité
        if ($currentCapacity === null) {
            return true;
        }

        $currentAttendeeCount = $slotOccurrence->attendees()->count();
        return $currentAttendeeCount < $currentCapacity;
    }
}
