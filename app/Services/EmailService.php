<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\SlotOccurence;
use App\Models\Slot;
use App\Models\Club;
use App\Notifications\SlotOccurenceCancelledNotification;
use App\Notifications\SlotOccurenceReminderNotification;
use App\Notifications\UserInvitationNotification;
use App\Notifications\WelcomeNotification;
use App\Notifications\SlotRegistrationNotification;
use App\Notifications\SlotUnregistrationNotification;
use App\Notifications\MonitorAssignmentNotification;
use App\Notifications\WeeklyScheduleNotification;
use App\Notifications\ClubAnnouncementNotification;
use Exception;

class EmailService
{
    /**
     * Envoyer une notification d'annulation de cours
     */
    public function sendCancellationNotification(SlotOccurence $slotOccurence, string $reason): bool
    {
        try {
            // Pour les tests, on envoie un email simple
            // En production, on récupérerait les participants depuis la base de données
            $date = $slotOccurence->date ? date('d/m/Y', strtotime($slotOccurence->date)) : 'Date non définie';
            $startTime = $slotOccurence->start_time ? date('H:i', strtotime($slotOccurence->start_time)) : 'Heure non définie';
            
            Mail::send('emails.test', [
                'testMessage' => "Le cours du {$date} à {$startTime} a été annulé. Raison : {$reason}"
            ], function ($message) use ($date) {
                $message->to('test@example.com')
                        ->subject("Annulation de cours - {$date}");
            });
            
            Log::info('Notification d\'annulation envoyée', [
                'slot_occurrence_id' => $slotOccurence->id ?? 'N/A',
                'reason' => $reason
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification d\'annulation', [
                'slot_occurrence_id' => $slotOccurence->id ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer un rappel de cours
     */
    public function sendReminderNotification(SlotOccurence $slotOccurence): bool
    {
        try {
            // Pour les tests, on envoie un email de rappel simple
            // En production, on récupérerait les participants depuis la base de données
            $date = $slotOccurence->date ? date('d/m/Y', strtotime($slotOccurence->date)) : 'Date non définie';
            $startTime = $slotOccurence->start_time ? date('H:i', strtotime($slotOccurence->start_time)) : 'Heure non définie';
            
            // Envoi d'un email de test pour le rappel
            Mail::send('emails.test', [
                'testMessage' => "Rappel : Vous avez un cours le {$date} à {$startTime}. N'oubliez pas de vous préparer !"
            ], function ($message) {
                $message->to('test@example.com')
                        ->subject('Rappel de cours');
            });
            
            Log::info('Rappel de cours envoyé', [
                'slot_occurrence_id' => $slotOccurence->id ?? 'N/A',
                'date' => $date,
                'time' => $startTime
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi du rappel', [
                'slot_occurrence_id' => $slotOccurence->id ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer une invitation d'utilisateur
     */
    public function sendUserInvitation(User $invitedUser, string $invitationToken, Club $club): bool
    {
        try {
            // Utiliser directement Mail::send au lieu de notify pour éviter les problèmes de persistance
            Mail::send('emails.custom', [
                'user' => $invitedUser,
                'message' => "Vous avez été invité à rejoindre le club {$club->name}. Cliquez sur le lien ci-dessous pour accepter l'invitation.",
                'data' => [
                    'action_url' => url("/register/{$invitationToken}"),
                    'action_text' => 'Accepter l\'invitation'
                ]
            ], function ($message) use ($invitedUser, $club) {
                $message->to($invitedUser->email, $invitedUser->firstname . ' ' . $invitedUser->name)
                        ->subject("Invitation - {$club->name}");
            });
            
            Log::info('Invitation utilisateur envoyée', [
                'user_email' => $invitedUser->email,
                'club_id' => $club->id ?? 'N/A'
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'invitation', [
                'user_email' => $invitedUser->email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer un email de bienvenue
     */
    public function sendWelcomeEmail(User $user, Club $club): bool
    {
        try {
            // Utiliser directement Mail::send au lieu de notify pour éviter les problèmes de persistance
            Mail::send('emails.custom', [
                'user' => $user,
                'message' => "Bienvenue au {$club->name} ! Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et commencer à utiliser notre plateforme.",
                'data' => [
                    'action_url' => url('/login'),
                    'action_text' => 'Se connecter'
                ]
            ], function ($message) use ($user, $club) {
                $message->to($user->email, $user->firstname . ' ' . $user->name)
                        ->subject("Bienvenue - {$club->name}");
            });
            
            Log::info('Email de bienvenue envoyé', [
                'user_id' => $user->id ?? 'N/A',
                'club_id' => $club->id ?? 'N/A'
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de bienvenue', [
                'user_id' => $user->id ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer une notification d'inscription à un cours
     */
    public function sendRegistrationNotification(User $user, SlotOccurence $slotOccurence): bool
    {
        try {
            // Utiliser directement Mail::send au lieu de notify pour éviter les problèmes de persistance
            $date = $slotOccurence->date ? date('d/m/Y', strtotime($slotOccurence->date)) : 'Date non définie';
            $startTime = $slotOccurence->start_time ? date('H:i', strtotime($slotOccurence->start_time)) : 'Heure non définie';
            
            Mail::send('emails.custom', [
                'user' => $user,
                'message' => "Votre inscription au cours du {$date} à {$startTime} a été confirmée. Nous vous attendons !",
                'data' => [
                    'action_url' => url('/dashboard'),
                    'action_text' => 'Voir mes cours'
                ]
            ], function ($message) use ($user, $date, $startTime) {
                $message->to($user->email, $user->firstname . ' ' . $user->name)
                        ->subject("Inscription confirmée - Cours du {$date}");
            });
            
            Log::info('Notification d\'inscription envoyée', [
                'user_id' => $user->id ?? 'N/A',
                'slot_occurrence_id' => $slotOccurence->id ?? 'N/A'
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification d\'inscription', [
                'user_id' => $user->id ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer une notification de désinscription
     */
    public function sendUnregistrationNotification(User $user, SlotOccurence $slotOccurence): bool
    {
        try {
            // Utiliser directement Mail::send au lieu de notify pour éviter les problèmes de persistance
            $date = $slotOccurence->date ? date('d/m/Y', strtotime($slotOccurence->date)) : 'Date non définie';
            $startTime = $slotOccurence->start_time ? date('H:i', strtotime($slotOccurence->start_time)) : 'Heure non définie';
            
            Mail::send('emails.custom', [
                'user' => $user,
                'message' => "Votre désinscription au cours du {$date} à {$startTime} a été confirmée. Nous espérons vous revoir bientôt !",
                'data' => [
                    'action_url' => url('/dashboard'),
                    'action_text' => 'Voir les cours disponibles'
                ]
            ], function ($message) use ($user, $date, $startTime) {
                $message->to($user->email, $user->firstname . ' ' . $user->name)
                        ->subject("Désinscription confirmée - Cours du {$date}");
            });
            
            Log::info('Notification de désinscription envoyée', [
                'user_id' => $user->id ?? 'N/A',
                'slot_occurrence_id' => $slotOccurence->id ?? 'N/A'
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification de désinscription', [
                'user_id' => $user->id ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer une notification d'assignation de moniteur
     */
    public function sendMonitorAssignmentNotification(User $monitor, SlotOccurence $slotOccurence): bool
    {
        try {
            // Utiliser directement Mail::send au lieu de notify pour éviter les problèmes de persistance
            $date = $slotOccurence->date ? date('d/m/Y', strtotime($slotOccurence->date)) : 'Date non définie';
            $startTime = $slotOccurence->start_time ? date('H:i', strtotime($slotOccurence->start_time)) : 'Heure non définie';
            
            Mail::send('emails.custom', [
                'user' => $monitor,
                'message' => "Vous avez été assigné comme moniteur pour le cours du {$date} à {$startTime}. Merci de votre disponibilité !",
                'data' => [
                    'action_url' => url('/dashboard'),
                    'action_text' => 'Voir mes cours'
                ]
            ], function ($message) use ($monitor, $date, $startTime) {
                $message->to($monitor->email, $monitor->firstname . ' ' . $monitor->name)
                        ->subject("Assignation moniteur - Cours du {$date}");
            });
            
            Log::info('Notification d\'assignation de moniteur envoyée', [
                'monitor_id' => $monitor->id ?? 'N/A',
                'slot_occurrence_id' => $slotOccurence->id ?? 'N/A'
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification d\'assignation', [
                'monitor_id' => $monitor->id ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer une annonce du club
     */
    public function sendClubAnnouncement(Club $club, string $subject, string $message): bool
    {
        try {
            // Pour les tests, on envoie un email simple
            // En production, on récupérerait les membres depuis la base de données
            Mail::send('emails.test', [
                'testMessage' => $message
            ], function ($mail) use ($subject) {
                $mail->to('test@example.com')
                     ->subject($subject);
            });
            
            Log::info('Annonce du club envoyée', [
                'club_id' => $club->id ?? 'N/A',
                'subject' => $subject
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'annonce', [
                'club_id' => $club->id ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoyer un email personnalisé
     */
    public function sendCustomEmail(User $user, string $subject, string $message, array $data = []): bool
    {
        try {
            Mail::send('emails.custom', [
                'user' => $user,
                'message' => $message,
                'data' => $data
            ], function ($mail) use ($user, $subject) {
                $mail->to($user->email, $user->firstname . ' ' . $user->name)
                     ->subject($subject);
            });
            
            Log::info('Email personnalisé envoyé', [
                'user_id' => $user->id,
                'subject' => $subject
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email personnalisé', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }



    /**
     * Vérifier la configuration email
     */
    public function testEmailConfiguration(): array
    {
        $config = config('mail');
        $results = [
            'mailer' => $config['default'],
            'from_address' => $config['from']['address'],
            'from_name' => $config['from']['name'],
            'status' => 'unknown'
        ];

        try {
            // Test simple d'envoi vers une adresse de test
            Mail::send('emails.test', [
                'testMessage' => 'Test de configuration email DogSchoolResa - ' . now()->format('Y-m-d H:i:s')
            ], function ($message) {
                $message->to('montuy.alexis@gmail.com')
                        ->subject('Test Configuration Email DogSchoolResa');
            });
            
            $results['status'] = 'success';
            $results['message'] = 'Configuration email valide';
        } catch (Exception $e) {
            $results['status'] = 'error';
            $results['message'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Test d'envoi d'email vers une adresse spécifique
     */
    public function testEmailSending(string $email): array
    {
        $results = [
            'email' => $email,
            'status' => 'unknown',
            'message' => '',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];

        try {
            // Test d'envoi d'email avec template simple
            Mail::send('emails.test', [
                'testMessage' => 'Test d\'envoi d\'email DogSchoolResa - ' . $results['timestamp']
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Envoi Email DogSchoolResa');
            });
            
            $results['status'] = 'success';
            $results['message'] = 'Email de test envoyé avec succès';
            
            Log::info('Test d\'envoi d\'email réussi', [
                'email' => $email,
                'timestamp' => $results['timestamp']
            ]);
            
        } catch (Exception $e) {
            $results['status'] = 'error';
            $results['message'] = $e->getMessage();
            
            Log::error('Échec du test d\'envoi d\'email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'timestamp' => $results['timestamp']
            ]);
        }

        return $results;
    }


}
