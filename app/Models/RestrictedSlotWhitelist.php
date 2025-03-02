<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestrictedSlotWhitelist extends Model
{
    protected $fillable = [
        'slot_id',
        'user_id',
    ];

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
