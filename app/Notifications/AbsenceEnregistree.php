<?php

namespace App\Notifications;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceEnregistree extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Nouvelle absence enregistrée')
                    ->line('Une nouvelle absence a été enregistrée à votre nom.')
                    ->line('Matière: ' . $this->absence->matiere->nom)
                    ->line('Date: ' . $this->absence->date_absence->format('d/m/Y'))
                    ->line('De: ' . $this->absence->heure_debut . ' à ' . $this->absence->heure_fin)
                    ->line('Type: ' . ucfirst($this->absence->type))
                    ->line('Motif: ' . ($this->absence->motif ?? 'Non précisé'))
                    ->action('Voir les détails', route('mes-absences.show', $this->absence->id))
                    ->line('Merci d\'utiliser notre application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'absence_enregistree',
            'absence_id' => $this->absence->id,
            'message' => 'Une absence a été enregistrée pour le ' . $this->absence->date_absence->format('d/m/Y') . 
                         ' en ' . $this->absence->matiere->nom,
            'url' => route('mes-absences.show', $this->absence->id)
        ];
    }
}
