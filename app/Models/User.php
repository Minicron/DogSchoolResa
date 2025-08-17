<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'firstname',
        'email',
        'password',
        'role',
        'club_id',
        'activation_token',
        'is_active',
        'calendar_view'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the club that owns the user.
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the clubs that the user is affiliated with.
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class);
    }

    /**
     * Get the slots the user is monitoring.
     */
    public function monitoredSlots()
    {
        return $this->hasMany(SlotOccurenceMonitor::class);
    }

    /**
     * Get the slots the user is attending.
     */
    public function attendedSlots()
    {
        return $this->hasMany(SlotOccurenceAttendee::class);
    }

    /**
     * Relation avec la liste d'attente
     */
    public function waitingList()
    {
        return $this->hasMany(WaitingList::class);
    }

    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Determine if the user is a member.
     *
     * @return bool
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
