<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvitation extends Model
{
    protected $fillable = [
        'email',
        'token',
        'club_id',
        'name',
        'firstname',
        'role',
        'is_sent',
        'is_accepted',
    ];

}
