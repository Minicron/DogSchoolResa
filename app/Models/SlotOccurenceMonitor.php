<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotOccurenceMonitor extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slot_occurence_id',
        'user_id',
    ];

    /**
     * Get the user that is enrolled in the course.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the slot occurrence that owns the monitor.
     */
    public function slotOccurence()
    {
        return $this->belongsTo(SlotOccurence::class);
    }

    /**
     * Get the slot that owns the time slot.
     */
    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Get the club that owns the time slot.
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
