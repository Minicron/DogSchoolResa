<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SlotOccurence as SlotOccurenceModel;

class WaitingListNotification extends Notification
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
        $monitorCount = $this->slotOccurrence->monitors()->count();
        
        return (new MailMessage)
            ->subject('Inscription en liste d\'attente - ' . $slot->name)
            ->greeting('Bonjour ' . $notifiable->firstname . ',')
            ->line('Votre inscription au cours **' . $slot->name . '** a été ajoutée à la liste d\'attente.')
            ->line('')
            ->line('**Détails du cours :**')
            ->line('• **Date :** ' . $courseDateTime->locale('fr')->isoFormat('dddd D MMMM YYYY'))
            ->line('• **Heure :** ' . $slot->start_time . ' - ' . $slot->end_time)
            ->line('• **Lieu :** ' . $slot->location)
            ->line('• **Moniteurs :** ' . $monitorCount . ' moniteur(s)')
            ->line('')
            ->line('Vous recevrez un email dès qu\'une place se libère.')
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
            'message' => 'Inscription en liste d\'attente pour le cours ' . $this->slotOccurrence->slot->name,
        ];
    }
}
