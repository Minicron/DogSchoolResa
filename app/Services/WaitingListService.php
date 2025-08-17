<?php

namespace App\Services;

use App\Models\SlotOccurence as SlotOccurenceModel;
use App\Models\SlotOccurenceAttendee;
use App\Models\WaitingList;
use App\Models\User;
use App\Notifications\WaitingListNotification;
use App\Notifications\PlaceAvailableNotification;
use Illuminate\Support\Facades\Log;

class WaitingListService
{
    /**
     * Ajouter un utilisateur en liste d'attente
     */
    public function addToWaitingList(User $user, SlotOccurenceModel $slotOccurrence): bool
    {
        try {
            // Vérifier si l'utilisateur n'est pas déjà en liste d'attente
            if (WaitingList::isUserWaiting($user->id, $slotOccurrence->id)) {
                Log::info('Utilisateur déjà en liste d\'attente', [
                    'user_id' => $user->id,
                    'slot_occurrence_id' => $slotOccurrence->id
                ]);
                return false;
            }

            // Vérifier si l'utilisateur n'est pas déjà inscrit
            if ($slotOccurrence->attendees()->where('user_id', $user->id)->exists()) {
                Log::info('Utilisateur déjà inscrit au cours', [
                    'user_id' => $user->id,
                    'slot_occurrence_id' => $slotOccurrence->id
                ]);
                return false;
            }

            // Ajouter en liste d'attente
            $waitingList = WaitingList::create([
                'user_id' => $user->id,
                'slot_occurence_id' => $slotOccurrence->id,
                'joined_at' => now(),
            ]);

            // Envoyer la notification
            $user->notify(new WaitingListNotification($slotOccurrence));

            Log::info('Utilisateur ajouté en liste d\'attente', [
                'user_id' => $user->id,
                'slot_occurrence_id' => $slotOccurrence->id,
                'waiting_list_id' => $waitingList->id
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout en liste d\'attente', [
                'user_id' => $user->id,
                'slot_occurrence_id' => $slotOccurrence->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Traiter la liste d'attente quand une place se libère
     */
    public function processWaitingList(SlotOccurenceModel $slotOccurrence): bool
    {
        try {
            // Vérifier s'il y a de la place disponible
            if (!$slotOccurrence->hasAvailableSpots()) {
                Log::info('Aucune place disponible pour traiter la liste d\'attente', [
                    'slot_occurrence_id' => $slotOccurrence->id
                ]);
                return false;
            }

            // Obtenir le prochain utilisateur en liste d'attente
            $nextWaitingUser = WaitingList::getNextWaitingUser($slotOccurrence->id);

            if (!$nextWaitingUser) {
                Log::info('Aucun utilisateur en liste d\'attente', [
                    'slot_occurrence_id' => $slotOccurrence->id
                ]);
                return false;
            }

            $user = $nextWaitingUser->user;

            // Vérifier que l'utilisateur n'est pas déjà inscrit
            if ($slotOccurrence->attendees()->where('user_id', $user->id)->exists()) {
                // Supprimer de la liste d'attente
                $nextWaitingUser->delete();
                Log::info('Utilisateur déjà inscrit, supprimé de la liste d\'attente', [
                    'user_id' => $user->id,
                    'slot_occurrence_id' => $slotOccurrence->id
                ]);
                return false;
            }

            // Inscrire l'utilisateur au cours
            SlotOccurenceAttendee::create([
                'slot_occurence_id' => $slotOccurrence->id,
                'user_id' => $user->id,
            ]);

            // Marquer comme notifié et supprimer de la liste d'attente
            $nextWaitingUser->markAsNotified();
            $nextWaitingUser->delete();

            // Envoyer la notification
            $user->notify(new PlaceAvailableNotification($slotOccurrence));

            Log::info('Utilisateur promu de la liste d\'attente vers le cours', [
                'user_id' => $user->id,
                'slot_occurrence_id' => $slotOccurrence->id
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de la liste d\'attente', [
                'slot_occurrence_id' => $slotOccurrence->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Vérifier et traiter automatiquement la liste d'attente
     */
    public function checkAndProcessWaitingList(SlotOccurenceModel $slotOccurrence): void
    {
        // Traiter la liste d'attente tant qu'il y a de la place et des utilisateurs en attente
        while ($slotOccurrence->hasAvailableSpots() && WaitingList::getNextWaitingUser($slotOccurrence->id)) {
            $this->processWaitingList($slotOccurrence);
        }
    }

    /**
     * Obtenir le nombre d'utilisateurs en liste d'attente
     */
    public function getWaitingListCount(SlotOccurenceModel $slotOccurrence): int
    {
        return $slotOccurrence->waitingList()->count();
    }

    /**
     * Vérifier si un utilisateur est en liste d'attente
     */
    public function isUserWaiting(User $user, SlotOccurenceModel $slotOccurrence): bool
    {
        return WaitingList::isUserWaiting($user->id, $slotOccurrence->id);
    }
}
