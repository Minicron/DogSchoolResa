<?php

namespace App\Console\Commands;

use App\Models\SlotOccurence;
use App\Models\User;
use App\Notifications\SlotOccurrenceClosingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSlotOccurrenceClosing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:check-closing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie la clôture des inscriptions pour les occurrences de cours et envoie des notifications aux admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de la vérification de la clôture des inscriptions...');

        // Récupérer toutes les occurrences futures qui ont une clôture automatique configurée
        $occurrences = SlotOccurence::with(['slot.club', 'attendees.user', 'monitors.user'])
            ->whereHas('slot', function ($query) {
                $query->where('auto_close', true)
                      ->whereNotNull('close_duration');
            })
            ->where('closing_notification_sent', false)
            ->where('is_cancelled', false)
            ->get()
            ->filter(function ($occurrence) {
                // Calculer la date et heure exacte du cours
                $courseDateTime = $occurrence->date->copy()->setTimeFromTimeString($occurrence->slot->start_time);
                
                // Vérifier que le cours est dans le futur (date + heure)
                return $courseDateTime->isFuture();
            });

        $this->info("Trouvé {$occurrences->count()} occurrence(s) à vérifier");

        $notificationsSent = 0;

        foreach ($occurrences as $occurrence) {
            if ($occurrence->isRegistrationClosed()) {
                $this->info("Clôture détectée pour le cours : {$occurrence->slot->name} - {$occurrence->date->format('d/m/Y')}");

                // Obtenir les statistiques
                $stats = $occurrence->getClosingStats();

                // Récupérer les admins du club
                $admins = User::where('club_id', $occurrence->slot->club_id)
                    ->whereIn('role', ['admin', 'admin-club', 'super_admin'])
                    ->get();

                if ($admins->isEmpty()) {
                    $this->warn("Aucun admin trouvé pour le club : {$occurrence->slot->club->name}");
                    continue;
                }

                // Envoyer la notification à tous les admins
                foreach ($admins as $admin) {
                    try {
                        $admin->notify(new SlotOccurrenceClosingNotification($occurrence, $stats));
                        $this->line("Notification envoyée à : {$admin->firstname} {$admin->name}");
                    } catch (\Exception $e) {
                        $this->error("Erreur lors de l'envoi de la notification à {$admin->email}: " . $e->getMessage());
                        Log::error("Erreur notification clôture", [
                            'admin_id' => $admin->id,
                            'occurrence_id' => $occurrence->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Marquer comme notification envoyée
                $occurrence->update(['closing_notification_sent' => true]);
                $notificationsSent++;

                $this->info("Rapport de clôture envoyé pour {$occurrence->slot->name}");
                $this->line("  - Participants : {$stats['attendee_count']}");
                $this->line("  - Moniteurs : {$stats['monitor_count']}");
            }
        }

        $this->info("Vérification terminée. {$notificationsSent} notification(s) envoyée(s)");

        return Command::SUCCESS;
    }
}
