<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'club_id',
        'name',
        'description',
        'location',
        'day_of_week',
        'time_start',
        'time_end',
        'capacity',
    ];

    /**
     * Get the club that owns the course.
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the occurrences of the slot.
     */
    public function occurrences()
    {
        return $this->hasMany(SlotOccurence::class);
    }
}
