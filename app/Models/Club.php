<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Club extends Model
{
    protected $fillable = [
        'name',
        'city',
        'description',
        'admin_id',
        'affiliates',
        'logo',
        'website',
        'facebook',
    ];

    /**
     * Relation : Admin du club
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relation : Tous les membres du club (y compris les moniteurs)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relation : Membres (sans les moniteurs)
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'member');
    }

    /**
     * Relation : Moniteurs
     */
    public function monitors(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'monitor');
    }

    /**
     * Relation : Créneaux horaires du club
     */
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    /**
     * Relation : Prochaines occurrences de cours
     */
    public function upcomingOccurrences()
    {
        return $this->hasManyThrough(SlotOccurence::class, Slot::class)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc');
    }
}
