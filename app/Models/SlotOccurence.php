<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotOccurence extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slot_id',
        'date',
        'is_cancelled',
        'is_full',
        'closing_notification_sent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'is_cancelled' => 'boolean',
        'is_full' => 'boolean',
        'closing_notification_sent' => 'boolean',
    ];

    /**
     * Get the slot that owns the time slot.
     */
    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Get the attendees for this slot occurrence.
     */
    public function attendees()
    {
        return $this->hasMany(SlotOccurenceAttendee::class, 'slot_occurence_id');
    }

    /**
     * Get the monitors for this slot occurrence.
     */
    public function monitors()
    {
        return $this->hasMany(SlotOccurenceMonitor::class, 'slot_occurence_id');
    }

    public function histories()
    {
        return $this->hasMany(SlotOccurenceHisto::class, 'slot_occurence_id')->latest();
    }

    public function cancellation()
    {
        return $this->hasOne(SlotOccurenceCancellation::class);
    }

    /**
     * Relation avec la liste d'attente
     */
    public function waitingList()
    {
        return $this->hasMany(WaitingList::class, 'slot_occurence_id');
    }

    /**
     * Vérifier si le cours est plein
     */
    public function isFull()
    {
        $currentCapacity = $this->slot->getCurrentCapacity($this);
        
        // Si pas de limite de capacité
        if ($currentCapacity === null) {
            return false;
        }

        $currentAttendeeCount = $this->attendees()->count();
        return $currentAttendeeCount >= $currentCapacity;
    }

    /**
     * Vérifier s'il y a de la place disponible
     */
    public function hasAvailableSpots()
    {
        return !$this->isFull();
    }

    /**
     * Obtenir le nombre de places disponibles
     */
    public function getAvailableSpots()
    {
        $currentCapacity = $this->slot->getCurrentCapacity($this);
        
        if ($currentCapacity === null) {
            return null; // Pas de limite
        }

        $currentAttendeeCount = $this->attendees()->count();
        return max(0, $currentCapacity - $currentAttendeeCount);
    }

    /**
     * Vérifier si les inscriptions sont closes pour cette occurrence
     */
    public function isRegistrationClosed()
    {
        // Si le slot n'a pas de clôture automatique, les inscriptions ne sont jamais closes
        if (!$this->slot->auto_close || !$this->slot->close_duration) {
            return false;
        }

        // Calculer la date et heure exacte du cours
        $courseDateTime = $this->date->copy()->setTimeFromTimeString($this->slot->start_time);
        
        // Calculer la date de clôture (cours - durée de clôture)
        $closingDate = $courseDateTime->copy()->subHours($this->slot->close_duration);
        
        return now()->gte($closingDate);
    }

    /**
     * Obtenir la date de clôture des inscriptions
     */
    public function getClosingDate()
    {
        if (!$this->slot->auto_close || !$this->slot->close_duration) {
            return null;
        }

        // Calculer la date et heure exacte du cours
        $courseDateTime = $this->date->copy()->setTimeFromTimeString($this->slot->start_time);
        
        // Retourner la date de clôture (cours - durée de clôture)
        return $courseDateTime->copy()->subHours($this->slot->close_duration);
    }

    /**
     * Obtenir les statistiques pour le rapport de clôture
     */
    public function getClosingStats()
    {
        $attendees = $this->attendees()->with('user')->get();
        $monitors = $this->monitors()->with('user')->get();

        return [
            'attendee_count' => $attendees->count(),
            'attendees' => $attendees->map(function ($attendee) {
                return [
                    'name' => $attendee->user->firstname . ' ' . $attendee->user->name,
                    'email' => $attendee->user->email,
                ];
            }),
            'monitor_count' => $monitors->count(),
            'monitors' => $monitors->map(function ($monitor) {
                return [
                    'name' => $monitor->user->firstname . ' ' . $monitor->user->name,
                    'email' => $monitor->user->email,
                ];
            }),
        ];
    }

    /**
     * Vérifier si les inscriptions sont closes et retourner un message approprié
     */
    public function getRegistrationStatus()
    {
        if ($this->isRegistrationClosed()) {
            return [
                'status' => 'closed',
                'message' => 'Inscriptions closes',
                'class' => 'text-red-600 bg-red-50'
            ];
        }

        $closingDate = $this->getClosingDate();
        if ($closingDate) {
            $now = now();
            $timeUntilClosing = $closingDate->diffForHumans($now, ['parts' => 2]);

            if ($closingDate->isPast()) {
                return [
                    'status' => 'closed',
                    'message' => 'Inscriptions closes',
                    'class' => 'text-red-600 bg-red-50'
                ];
            } elseif ($closingDate->diffInHours($now) <= 24) {
                return [
                    'status' => 'closing_soon',
                    'message' => "Clôture dans {$timeUntilClosing}",
                    'class' => 'text-orange-600 bg-orange-50'
                ];
            } else {
                return [
                    'status' => 'open',
                    'message' => "Clôture dans {$timeUntilClosing}",
                    'class' => 'text-green-600 bg-green-50'
                ];
            }
        }

        return [
            'status' => 'open',
            'message' => 'Inscriptions ouvertes',
            'class' => 'text-green-600 bg-green-50'
        ];
    }

    /**
     * Vérifier si l'utilisateur peut s'inscrire (inscriptions ouvertes et pas déjà inscrit)
     */
    public function canUserRegister($userId)
    {
        if ($this->isRegistrationClosed()) {
            return false;
        }

        // Vérifier si déjà inscrit comme participant ou moniteur
        $isAttendee = $this->attendees()->where('user_id', $userId)->exists();
        $isMonitor = $this->monitors()->where('user_id', $userId)->exists();

        return !$isAttendee && !$isMonitor;
    }

}
