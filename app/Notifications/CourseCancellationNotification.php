<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SlotOccurence;

class CourseCancellationNotification extends Notification
{
    use Queueable;

    protected $slotOccurrence;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(SlotOccurence $slotOccurrence, string $reason)
    {
        $this->slotOccurrence = $slotOccurrence;
        $this->reason = $reason;
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
        $courseDateTime = $this->slotOccurrence->date->copy()->setTimeFromTimeString($slot->start_time);
        
        return (new MailMessage)
            ->subject('Cours annulé - ' . $slot->name)
            ->greeting('Bonjour ' . $notifiable->firstname . ',')
            ->line('Le cours **' . $slot->name . '** du ' . $courseDateTime->locale('fr')->isoFormat('dddd D MMMM YYYY') . ' a été annulé.')
            ->line('')
            ->line('**Raison :** ' . $this->reason)
            ->line('')
            ->line('**Détails du cours annulé :**')
            ->line('• **Heure :** ' . $slot->start_time . ' - ' . $slot->end_time)
            ->line('• **Lieu :** ' . $slot->location)
            ->line('')
            ->line('Nous vous remercions de votre compréhension.')
            ->salutation('L\'équipe CEC Condat');
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
            'reason' => $this->reason,
            'message' => 'Cours annulé : ' . $this->slotOccurrence->slot->name,
        ];
    }
}
