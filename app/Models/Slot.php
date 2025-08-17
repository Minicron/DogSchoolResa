<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = [
        'club_id',
        'name',
        'description',
        'location',
        'day_of_week',
        'start_time',
        'end_time',
        'capacity',
        'capacity_type',
        'alert_monitors',
        'auto_close',
        'close_duration',
        'is_restricted',
    ];

    protected $casts = [
        'is_restricted' => 'boolean',
        'has_groups' => 'boolean',
        'auto_close' => 'boolean',
        'close_duration' => 'integer',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function occurrences()
    {
        return $this->hasMany(SlotOccurence::class);
    }

    // Nouvelle relation pour la whitelist
    public function whitelist()
    {
        return $this->hasMany(RestrictedSlotWhitelist::class);
    }

    public function slotGroups()
    {
        return $this->hasMany(SlotGroup::class)->orderBy('order');
    }

    /**
     * Calcule la capacité actuelle en fonction du type de capacité
     */
    public function getCurrentCapacity(?SlotOccurence $occurrence = null)
    {
        switch ($this->capacity_type) {
            case 'none':
                return null; // Pas de limite
            case 'fixed':
                return $this->capacity;
            case 'dynamic':
                if ($occurrence) {
                    $monitorCount = $occurrence->monitors()->count();
                    return $monitorCount * 5;
                }
                return 0;
            default:
                return $this->capacity;
        }
    }


}
