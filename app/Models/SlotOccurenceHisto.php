<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotOccurenceHisto extends Model
{
    use HasFactory;

    protected $table = 'slot_occurence_histos'; // Nom de la table

    protected $fillable = [
        'slot_occurence_id',
        'user_id',
        'action',
        'details',
        'created_at'
    ];

    // Définition des constantes pour les actions possibles
    public const ACTION_CREATED = 'created';
    public const ACTION_UPDATED = 'updated';
    public const ACTION_DELETED = 'deleted';
    public const ACTION_MONITOR_ASSIGNED = 'monitor_assigned';
    public const ACTION_MONITOR_REMOVED = 'monitor_removed';
    public const ACTION_ATTENDEE_ADDED = 'attendee_added';
    public const ACTION_ATTENDEE_REMOVED = 'attendee_removed';

    /**
     * Relation avec l'occurrence du créneau.
     */
    public function slotOccurrence()
    {
        return $this->belongsTo(SlotOccurrence::class);
    }

    /**
     * Relation avec l'utilisateur ayant effectué l'action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
