<?php

namespace App\Notifications;

use App\Models\SlotOccurence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlotOccurrenceClosingNotification extends Notification
{
    use Queueable;

    protected $slotOccurrence;
    protected $stats;

    /**
     * Create a new notification instance.
     */
    public function __construct(SlotOccurence $slotOccurrence, array $stats)
    {
        $this->slotOccurrence = $slotOccurrence;
        $this->stats = $stats;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $slot = $this->slotOccurrence->slot;
        $club = $slot->club;

        $attendeeList = $this->stats['attendees']->map(function ($attendee) {
            return "• {$attendee['name']} ({$attendee['email']})";
        })->join("\n");

        $monitorList = $this->stats['monitors']->map(function ($monitor) {
            return "• {$monitor['name']} ({$monitor['email']})";
        })->join("\n");

        return (new MailMessage)
            ->subject("Clôture des inscriptions - {$slot->name} - {$club->name}")
            ->greeting("Bonjour {$notifiable->firstname},")
            ->line("Les inscriptions pour le cours suivant sont maintenant closes :")
            ->line("")
            ->line("**Cours :** {$slot->name}")
            ->line("**Date :** {$this->slotOccurrence->date->format('d/m/Y')}")
            ->line("**Heure :** {$slot->start_time} - {$slot->end_time}")
            ->line("**Lieu :** {$slot->location}")
            ->line("")
            ->line("**Résumé des inscriptions :**")
            ->line("• Participants inscrits : {$this->stats['attendee_count']}")
            ->line("• Moniteurs présents : {$this->stats['monitor_count']}")
            ->line("")
            ->when($this->stats['attendee_count'] > 0, function ($message) use ($attendeeList) {
                return $message->line("**Liste des participants :**")
                    ->line($attendeeList);
            })
            ->when($this->stats['monitor_count'] > 0, function ($message) use ($monitorList) {
                return $message->line("**Liste des moniteurs :**")
                    ->line($monitorList);
            })
            ->when($this->stats['attendee_count'] === 0, function ($message) {
                return $message->line("⚠️ **Aucun participant inscrit**");
            })
            ->when($this->stats['monitor_count'] === 0, function ($message) {
                return $message->line("⚠️ **Aucun moniteur inscrit**");
            })
            ->line("")
            ->line("Cordialement,")
            ->salutation("L'équipe {$club->name}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'slot_occurrence_id' => $this->slotOccurrence->id,
            'slot_name' => $this->slotOccurrence->slot->name,
            'date' => $this->slotOccurrence->date->format('Y-m-d'),
            'attendee_count' => $this->stats['attendee_count'],
            'monitor_count' => $this->stats['monitor_count'],
        ];
    }
}
