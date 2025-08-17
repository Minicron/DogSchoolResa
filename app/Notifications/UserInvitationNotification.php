<?php

namespace App\Notifications;

use App\Models\Club;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification
{
    use Queueable;

    protected User $user;
    protected string $token;
    protected Club $club;

    public function __construct(User $user, string $token, Club $club)
    {
        $this->user = $user;
        $this->token = $token;
        $this->club = $club;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('user.registerFromMail', [
            'token' => $this->token,
        ], false));

        return (new MailMessage)
            ->subject('Invitation à rejoindre ' . $this->club->name)
            ->greeting('Bonjour ' . $this->user->firstname . ',')
            ->line('Vous avez été invité à rejoindre le club **' . $this->club->name . '**.')
            ->line('')
            ->line('**Votre rôle :** ' . $this->getRoleDescription($this->user->role))
            ->line('')
            ->line('**Capacités :**')
            ->line($this->getRoleCapabilities($this->user->role))
            ->line('')
            ->action('Accepter l\'invitation', $url)
            ->line('Ce lien d\'invitation expirera dans 7 jours.')
            ->salutation('L\'équipe CEC Condat');
    }

    private function getRoleDescription(string $role): string
    {
        return match($role) {
            'admin' => 'Administrateur du club',
            'super_admin' => 'Super administrateur',
            default => 'Membre du club'
        };
    }

    private function getRoleCapabilities(string $role): string
    {
        return match($role) {
            'admin' => '• Gérer les cours et les membres du club',
            'super_admin' => '• Accès complet à toutes les fonctionnalités',
            default => '• Participer aux cours et activités'
        };
    }
}
