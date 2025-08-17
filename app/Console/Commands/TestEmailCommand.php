<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\EmailService;
use App\Models\User;
use App\Models\Club;
use App\Models\SlotOccurence;
use Exception;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email 
                            {email : Adresse email de test}
                            {--type=simple : Type de test (simple, welcome, invitation, registration, cancellation, reminder, announcement, custom)}
                            {--message= : Message personnalisé pour le test custom}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test d\'envoi d\'email avec différents types de notifications';

    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');
        $message = $this->option('message');

        $this->info('📧 Test d\'envoi d\'email DogSchoolResa');
        $this->info('=====================================');
        $this->newLine();

        // Afficher la configuration
        $this->displayConfiguration();

        // Test selon le type
        switch ($type) {
            case 'simple':
                $this->testSimpleEmail($email);
                break;
            case 'welcome':
                $this->testWelcomeEmail($email);
                break;
            case 'invitation':
                $this->testInvitationEmail($email);
                break;
            case 'registration':
                $this->testRegistrationEmail($email);
                break;
            case 'cancellation':
                $this->testCancellationEmail($email);
                break;
            case 'reminder':
                $this->testReminderEmail($email);
                break;
            case 'announcement':
                $this->testAnnouncementEmail($email);
                break;
            case 'custom':
                $this->testCustomEmail($email, $message);
                break;
            default:
                $this->error("Type de test inconnu: {$type}");
                $this->line('Types disponibles: simple, welcome, invitation, registration, cancellation, reminder, announcement, custom');
                return 1;
        }

        return 0;
    }

    /**
     * Afficher la configuration email
     */
    private function displayConfiguration()
    {
        $this->info('🔧 Configuration email:');
        
        $config = config('mail');
        
        $this->line("  📋 Mailer: {$config['default']}");
        $this->line("  📋 From: {$config['from']['address']} ({$config['from']['name']})");
        
        if ($config['default'] === 'smtp') {
            $this->line("  📋 Host: " . env('MAIL_HOST', 'Non défini'));
            $this->line("  📋 Port: " . env('MAIL_PORT', 'Non défini'));
            $this->line("  📋 Username: " . env('MAIL_USERNAME', 'Non défini'));
            $this->line("  📋 Encryption: " . env('MAIL_ENCRYPTION', 'Non défini'));
        }
        
        $this->newLine();
    }

    /**
     * Test d'email simple
     */
    private function testSimpleEmail($email)
    {
        $this->info("📤 Test d'email simple vers {$email}...");

        try {
            $results = $this->emailService->testEmailSending($email);

            if ($results['status'] === 'success') {
                $this->line("  ✅ Email simple envoyé avec succès");
                $this->line("  📋 Timestamp: {$results['timestamp']}");
            } else {
                $this->line("  ❌ Échec de l'envoi: {$results['message']}");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test d'email de bienvenue
     */
    private function testWelcomeEmail($email)
    {
        $this->info("📤 Test d'email de bienvenue vers {$email}...");

        try {
            $user = new User([
                'name' => 'Test',
                'firstname' => 'Utilisateur',
                'email' => $email,
            ]);

            $club = new Club([
                'name' => 'Club d\'Éducation Canine de Condat-Sur-Vienne',
                'city' => 'Condat-Sur-Vienne',
            ]);

            $success = $this->emailService->sendWelcomeEmail($user, $club);

            if ($success) {
                $this->line("  ✅ Email de bienvenue envoyé avec succès");
            } else {
                $this->line("  ❌ Échec de l'envoi de l'email de bienvenue");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test d'email d'invitation
     */
    private function testInvitationEmail($email)
    {
        $this->info("📤 Test d'email d'invitation vers {$email}...");

        try {
            $user = new User([
                'name' => 'Test',
                'firstname' => 'Utilisateur',
                'email' => $email,
            ]);

            $club = new Club([
                'name' => 'Club d\'Éducation Canine de Condat-Sur-Vienne',
                'city' => 'Condat-Sur-Vienne',
            ]);

            $token = 'test-invitation-token-' . time();

            $success = $this->emailService->sendUserInvitation($user, $token, $club);

            if ($success) {
                $this->line("  ✅ Email d'invitation envoyé avec succès");
            } else {
                $this->line("  ❌ Échec de l'envoi de l'email d'invitation");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test d'email d'inscription
     */
    private function testRegistrationEmail($email)
    {
        $this->info("📤 Test d'email d'inscription vers {$email}...");

        try {
            $user = new User([
                'name' => 'Test',
                'firstname' => 'Utilisateur',
                'email' => $email,
            ]);

            $slotOccurence = new SlotOccurence([
                'id' => 1,
                'date' => now()->addDays(7),
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
            ]);

            $success = $this->emailService->sendRegistrationNotification($user, $slotOccurence);

            if ($success) {
                $this->line("  ✅ Email d'inscription envoyé avec succès");
            } else {
                $this->line("  ❌ Échec de l'envoi de l'email d'inscription");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test d'email d'annulation
     */
    private function testCancellationEmail($email)
    {
        $this->info("📤 Test d'email d'annulation vers {$email}...");

        try {
            $slotOccurence = new SlotOccurence([
                'id' => 1,
                'date' => now()->addDays(7),
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
            ]);

            $success = $this->emailService->sendCancellationNotification($slotOccurence, 'Test d\'annulation');

            if ($success) {
                $this->line("  ✅ Email d'annulation envoyé avec succès");
            } else {
                $this->line("  ❌ Échec de l'envoi de l'email d'annulation");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test d'email de rappel
     */
    private function testReminderEmail($email)
    {
        $this->info("📤 Test d'email de rappel vers {$email}...");

        try {
            $slotOccurence = new SlotOccurence([
                'id' => 1,
                'date' => now()->addDays(1),
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
            ]);

            $success = $this->emailService->sendReminderNotification($slotOccurence);

            if ($success) {
                $this->line("  ✅ Email de rappel envoyé avec succès");
            } else {
                $this->line("  ❌ Échec de l'envoi de l'email de rappel");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test d'email d'annonce
     */
    private function testAnnouncementEmail($email)
    {
        $this->info("📤 Test d'email d'annonce vers {$email}...");

        try {
            $club = new Club([
                'name' => 'Club d\'Éducation Canine de Condat-Sur-Vienne',
                'city' => 'Condat-Sur-Vienne',
            ]);

            $success = $this->emailService->sendClubAnnouncement($club, 'Test d\'annonce', 'Ceci est un test d\'annonce du club.');

            if ($success) {
                $this->line("  ✅ Email d'annonce envoyé avec succès");
            } else {
                $this->line("  ❌ Échec de l'envoi de l'email d'annonce");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Test d'email personnalisé
     */
    private function testCustomEmail($email, $message = null)
    {
        $this->info("📤 Test d'email personnalisé vers {$email}...");

        try {
            $user = new User([
                'name' => 'Test',
                'firstname' => 'Utilisateur',
                'email' => $email,
            ]);

            $customMessage = $message ?: 'Ceci est un test d\'email personnalisé.';

            $success = $this->emailService->sendCustomEmail($user, 'Test Email Personnalisé', $customMessage);

            if ($success) {
                $this->line("  ✅ Email personnalisé envoyé avec succès");
            } else {
                $this->line("  ❌ Échec de l'envoi de l'email personnalisé");
            }

        } catch (Exception $e) {
            $this->line("  ❌ Erreur: {$e->getMessage()}");
        }

        $this->newLine();
    }
}
