<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Slot;
use App\Models\SlotOccurence;

class SlotScheduleChangeNotification extends Notification
{
    use Queueable;

    protected $slot;
    protected $occurrence;
    protected $oldSchedule;
    protected $newSchedule;

    /**
     * Create a new notification instance.
     */
    public function __construct(Slot $slot, SlotOccurence $occurrence, array $oldSchedule, array $newSchedule)
    {
        $this->slot = $slot;
        $this->occurrence = $occurrence;
        $this->oldSchedule = $oldSchedule;
        $this->newSchedule = $newSchedule;
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
        $daysOfWeek = [
            1 => 'Lundi',
            2 => 'Mardi', 
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche'
        ];

        $oldDay = $daysOfWeek[$this->oldSchedule['day']] ?? 'Inconnu';
        $newDay = $daysOfWeek[$this->newSchedule['day']] ?? 'Inconnu';

        return (new MailMessage)
            ->subject('Changement d\'horaire - ' . $this->slot->name)
            ->greeting('Bonjour ' . $notifiable->firstname . ' !')
            ->line('Nous vous informons que l\'horaire du cours **' . $this->slot->name . '** a été modifié.')
            ->line('')
            ->line('**Ancien horaire :**')
            ->line('• Jour : ' . $oldDay)
            ->line('• Heure : ' . $this->oldSchedule['start_time'] . ' - ' . $this->oldSchedule['end_time'])
            ->line('')
            ->line('**Nouvel horaire :**')
            ->line('• Jour : ' . $newDay)
            ->line('• Heure : ' . $this->newSchedule['start_time'] . ' - ' . $this->newSchedule['end_time'])
            ->line('')
            ->line('**Conséquences :**')
            ->line('• Tous les cours futurs de ce créneau ont été annulés')
            ->line('• De nouveaux cours ont été créés avec le nouvel horaire')
            ->line('• Vous devrez vous réinscrire aux nouveaux cours si vous souhaitez y participer')
            ->line('')
            ->line('Nous vous invitons à consulter le calendrier pour vous inscrire aux nouveaux cours.')
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
            'slot_id' => $this->slot->id,
            'slot_name' => $this->slot->name,
            'occurrence_id' => $this->occurrence->id,
            'old_schedule' => $this->oldSchedule,
            'new_schedule' => $this->newSchedule,
        ];
    }
}
