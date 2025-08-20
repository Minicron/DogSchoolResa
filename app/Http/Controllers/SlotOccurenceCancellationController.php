<?php

namespace App\Http\Controllers;

use App\Models\SlotOccurence;
use App\Models\SlotOccurenceCancellation;
use App\Models\SlotOccurenceHisto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SlotOccurenceCancelledNotification;

class SlotOccurenceCancellationController extends Controller
{
    public function create($id)
    {
        $slotOccurence = SlotOccurence::findOrFail($id);

        // Liste des raisons prédéfinies
        $reasons = [
            'Mauvais temps',
            'Manque de moniteurs',
            'Événement exceptionnel',
            'Problème de disponibilité des infrastructures',
            'Autre'
        ];

        return view('AdminClub.partials.cancel_slot_occurence', compact('slotOccurence', 'reasons'));
    }

    public function store(Request $request, $slotOccurenceId)
    {
        $slotOccurence = SlotOccurence::findOrFail($slotOccurenceId);
    
        if ($slotOccurence->is_cancelled) {
            return response()->json(['error' => 'Cette occurrence est déjà annulée.'], 400);
        }
    
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);
    
        $slotOccurence->update(['is_cancelled' => true]);
        
        $slotOccurence->cancellation()->create([
            'reason' => $request->input('reason'),
            'user_id' => auth()->id()
        ]);

        $slotOccurence->histories()->create([
            'action' => 'Annulation',
            'reason' => $request->input('reason'),
            'user_id' => auth()->id(),
        ]);

        // Envoyer les notifications par email
        $emailService = app(\App\Services\EmailService::class);
        $emailService->sendCancellationNotification($slotOccurence, $request->input('reason'));

        // Retourner une réponse qui ferme la modal et rafraîchit la page
        return response()->json([
            'success' => true,
            'message' => 'Cours annulé avec succès',
            'redirect' => true
        ]);
    }

}
