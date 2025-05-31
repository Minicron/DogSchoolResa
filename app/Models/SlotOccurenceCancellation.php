<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotOccurenceCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_occurence_id',
        'user_id',
        'reason',
    ];

    /**
     * Relation avec l'occurrence de créneau.
     */
    public function slotOccurence()
    {
        return $this->belongsTo(SlotOccurence::class);
    }

    /**
     * Relation avec l'utilisateur ayant annulé.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
