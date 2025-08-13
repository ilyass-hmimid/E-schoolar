<?php

namespace App\Listeners;

use App\Events\NewNotificationBroadcasted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class BroadcastNotificationCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            // Vérifier si la notification a un utilisateur associé
            if (!isset($event->notifiable) || !$event->notifiable) {
                return;
            }

            // Récupérer l'ID de l'utilisateur
            $userId = $event->notifiable->id;
            
            // Préparer les données de la notification pour la diffusion
            $notificationData = [
                'id' => $event->notification->id,
                'type' => get_class($event->notification),
                'data' => $event->notification->data,
                'created_at' => $event->notification->created_at->toDateTimeString(),
                'read_at' => null,
            ];

            // Diffuser la notification en temps réel
            event(new NewNotificationBroadcasted($notificationData, $userId));
            
        } catch (\Exception $e) {
            // Enregistrer les erreurs dans les logs
            Log::error('Erreur lors de la diffusion de la notification: ' . $e->getMessage(), [
                'exception' => $e,
                'notification_id' => $event->notification->id ?? null,
                'notifiable_id' => $event->notifiable->id ?? null,
            ]);
        }
    }
}
