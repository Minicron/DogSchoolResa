<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitingList extends Model
{
    protected $fillable = [
        'user_id',
        'slot_occurence_id',
        'joined_at',
        'is_notified',
        'notified_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'notified_at' => 'datetime',
        'is_notified' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'occurrence de cours
     */
    public function slotOccurrence(): BelongsTo
    {
        return $this->belongsTo(SlotOccurence::class, 'slot_occurence_id');
    }

    /**
     * Relation avec le slot (cours)
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'slot_id', 'id', 'slot_occurences');
    }

    /**
     * Marquer comme notifié
     */
    public function markAsNotified(): void
    {
        $this->update([
            'is_notified' => true,
            'notified_at' => now(),
        ]);
    }

    /**
     * Vérifier si l'utilisateur est en liste d'attente pour une occurrence
     */
    public static function isUserWaiting(int $userId, int $slotOccurrenceId): bool
    {
        return static::where('user_id', $userId)
            ->where('slot_occurence_id', $slotOccurrenceId)
            ->exists();
    }

    /**
     * Obtenir le prochain utilisateur en liste d'attente pour une occurrence
     */
    public static function getNextWaitingUser(int $slotOccurrenceId): ?self
    {
        return static::where('slot_occurence_id', $slotOccurrenceId)
            ->where('is_notified', false)
            ->orderBy('joined_at', 'asc')
            ->first();
    }

    /**
     * Obtenir tous les utilisateurs en liste d'attente pour une occurrence
     */
    public static function getWaitingUsers(int $slotOccurrenceId)
    {
        return static::where('slot_occurence_id', $slotOccurrenceId)
            ->with('user')
            ->orderBy('joined_at', 'asc')
            ->get();
    }
}
