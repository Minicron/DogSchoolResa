<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SlotOccurence as SlotOccurenceModel;

class PlaceAvailableNotification extends Notification
{
    use Queueable;

    protected $slotOccurrence;

    /**
     * Create a new notification instance.
     */
    public function __construct(SlotOccurenceModel $slotOccurrence)
    {
        $this->slotOccurrence = $slotOccurrence;
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
            ->subject('Place disponible - ' . $slot->name)
            ->greeting('Bonjour ' . $notifiable->firstname . ',')
            ->line('Une place s\'est libérée pour le cours **' . $slot->name . '**.')
            ->line('')
            ->line('**Détails du cours :**')
            ->line('• **Date :** ' . $courseDateTime->locale('fr')->isoFormat('dddd D MMMM YYYY'))
            ->line('• **Heure :** ' . $slot->start_time . ' - ' . $slot->end_time)
            ->line('• **Lieu :** ' . $slot->location)
            ->line('')
            ->line('Votre inscription a été confirmée automatiquement.')
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
            'message' => 'Place disponible pour le cours ' . $this->slotOccurrence->slot->name,
        ];
    }
}
