<?php

// app/Models/SlotGroup.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotGroup extends Model
{
    protected $fillable = [
        'slot_id',
        'name',
        'order',
    ];

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }
}
