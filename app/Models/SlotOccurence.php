<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotOccurence extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slot_id',
        'date',
        'is_cancelled',
        'is_full',
    ];

    /**
     * Get the slot that owns the time slot.
     */
    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Get the attendees for this slot occurrence.
     */
    public function attendees()
    {
        return $this->hasMany(SlotOccurenceAttendee::class, 'slot_occurence_id');
    }

    /**
     * Get the monitors for this slot occurrence.
     */
    public function monitors()
    {
        return $this->hasMany(SlotOccurenceMonitor::class, 'slot_occurence_id');
    }
}
