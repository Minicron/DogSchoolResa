<?php

namespace App\Notifications;

use App\Models\SlotOccurence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DynamicCapacityExceededNotification extends Notification
{
    use Queueable;

    protected SlotOccurence $slotOccurrence;
    protected int $monitorCount;
    protected int $attendeeCount;
    protected int $newCapacity;
    protected int $excessCount;

    public function __construct(SlotOccurence $slotOccurrence, int $monitorCount, int $attendeeCount, int $newCapacity, int $excessCount)
    {
        $this->slotOccurrence = $slotOccurrence;
        $this->monitorCount = $monitorCount;
        $this->attendeeCount = $attendeeCount;
        $this->newCapacity = $newCapacity;
        $this->excessCount = $excessCount;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $slot = $this->slotOccurrence->slot;
        $courseDateTime = $this->slotOccurrence->date->copy()->setTimeFromTimeString($slot->start_time);
        $monitors = $this->slotOccurrence->monitors()->with('user')->get();
        $monitorNames = $monitors->map(function($monitor) {
            return $monitor->user->firstname . ' ' . $monitor->user->name;
        })->join(', ');
        
        return (new MailMessage)
            ->subject('Alerte capacité - ' . $slot->name)
            ->greeting('Bonjour ' . $notifiable->firstname . ',')
            ->line('Un moniteur s\'est désinscrit du cours **' . $slot->name . '**.')
            ->line('')
            ->line('**Situation actuelle :**')
            ->line('• **Moniteurs :** ' . $this->monitorCount . ' moniteur(s)')
            ->line('• **Capacité :** ' . $this->newCapacity . ' participants maximum')
            ->line('• **Inscrits :** ' . $this->attendeeCount . ' participants')
            ->line('• **Excédent :** ' . $this->excessCount . ' participant(s) en trop')
            ->line('')
            ->line('**Détails du cours :**')
            ->line('• **Date :** ' . $courseDateTime->locale('fr')->isoFormat('dddd D MMMM YYYY'))
            ->line('• **Heure :** ' . $slot->start_time . ' - ' . $slot->end_time)
            ->line('• **Lieu :** ' . $slot->location)
            ->line('• **Moniteurs présents :** ' . $monitorNames)
            ->line('')
            ->line('**Action requise :**')
            ->line('Vous devez soit ajouter des moniteurs, soit déplacer des participants vers la liste d\'attente.')
            ->salutation('L\'équipe CEC Condat');
    }
}
