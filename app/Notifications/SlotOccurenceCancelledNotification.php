<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\SlotOccurence;

class SlotOccurenceCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $slotOccurence;
    protected $reason;

    public function __construct(SlotOccurence $slotOccurence, $reason)
    {
        $this->slotOccurence = $slotOccurence;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Annulation de votre cours')
            ->greeting('Bonjour ' . $notifiable->firstname)
            ->line('Le cours prévu le ' . $this->slotOccurence->date . ' a été annulé.')
            ->line('Raison : ' . $this->reason)
            ->line('Nous nous excusons pour la gêne occasionnée.')
            ->action('Voir les autres créneaux', url('/dashboard'))
            ->line('Merci de votre compréhension.');
    }
}
