<?php

namespace App\Listeners;

use App\Events\AbsenceCreated;
use App\Notifications\AbsenceCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendAbsenceCreatedNotification implements ShouldQueue
{
    /**
     * Le nom de la file de connexion que le travail doit envoyer.
     *
     * @var string|null
     */
    public $connection = 'database';

    /**
     * Le nom de la file dans laquelle le travail doit être envoyé.
     *
     * @var string|null
     */
    public $queue = 'notifications';

    /**
     * Gère l'événement.
     *
     * @param  \App\Events\AbsenceCreated  $event
     * @return void
     */
    public function handle(AbsenceCreated $event)
    {
        $absence = $event->absence;
        
        // Envoyer une notification à l'élève
        if ($absence->eleve) {
            $absence->eleve->notify(new AbsenceCreatedNotification($absence, 'Élève'));
        }
        
        // Envoyer une notification au professeur
        if ($absence->professeur) {
            $absence->professeur->notify(new AbsenceCreatedNotification($absence, 'Professeur'));
        }
        
        // Envoyer une notification aux administrateurs
        $admins = \App\Models\User::where('role', 'admin')->get();
        Notification::send($admins, new AbsenceCreatedNotification($absence, 'Administrateur'));
    }
}
