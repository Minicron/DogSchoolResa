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
        'alert_monitors',
        'auto_close',
        'close_duration',
        'is_restricted',
    ];

    protected $casts = [
        'is_restricted' => 'boolean',
        'has_groups' => 'boolean',
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

}
