<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Absence;

class EleveAbsentNonPaye extends Notification implements ShouldQueue
{
    use Queueable;

    protected $absence;

    /**
     * Create a new notification instance.
     *
     * @param Absence $absence
     * @return void
     */
    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $matiere = $this->absence->matiere->nom ?? 'Matière non spécifiée';
        $date = $this->absence->date_absence->format('d/m/Y');
        
        return (new MailMessage)
                    ->subject("Absence non justifiée - {$matiere} - {$date}")
                    ->line("L'élève {$this->absence->etudiant->nom} {$this->absence->etudiant->prenom} était absent(e) le {$date} pour le cours de {$matiere}.")
                    ->line('Cette absence n\'a pas encore été justifiée ou payée.')
                    ->action('Voir les détails', route('eleve.absences.show', $this->absence->id))
                    ->line('Merci d\'utiliser notre application !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $matiere = $this->absence->matiere->nom ?? 'Matière non spécifiée';
        $date = $this->absence->date_absence->format('d/m/Y');
        
        return [
            'type' => 'absence_non_justifiee',
            'message' => "Absence non justifiée pour le cours de {$matiere} du {$date}",
            'absence_id' => $this->absence->id,
            'etudiant_id' => $this->absence->etudiant_id,
            'date_absence' => $this->absence->date_absence->toDateString(),
        ];
    }
}
