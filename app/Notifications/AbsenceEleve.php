<?php

namespace App\Notifications;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceEleve extends Notification implements ShouldQueue
{
    use Queueable;

    protected $absence;
    protected $eleve;

    /**
     * Create a new notification instance.
     *
     * @param Absence $absence
     * @param User $eleve
     * @return void
     */
    public function __construct(Absence $absence, User $eleve)
    {
        $this->absence = $absence;
        $this->eleve = $eleve;
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
                    ->subject('Absence de votre enfant - ' . $this->eleve->prenom . ' ' . $this->eleve->nom)
                    ->greeting('Bonjour ' . $notifiable->prenom . ',')
                    ->line('Nous vous informons que votre enfant ' . $this->eleve->prenom . ' ' . $this->eleve->nom . ' a été marqué(e) absent(e) aujourd\'hui.')
                    ->line('Détails de l\'absence :')
                    ->line('- Matière: ' . $this->absence->matiere->nom)
                    ->line('- Date: ' . $this->absence->date_absence->format('d/m/Y'))
                    ->line('- Période: De ' . $this->absence->heure_debut . ' à ' . $this->absence->heure_fin)
                    ->line('- Type: ' . ucfirst($this->absence->type))
                    ->line('- Motif: ' . ($this->absence->motif ?? 'Non précisé'))
                    ->line('')
                    ->line('Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter l\'administration de l\'établissement.')
                    ->action('Voir le détail', route('parent.absences.show', $this->absence->id))
                    ->line('Cordialement,')
                    ->salutation('L\'équipe ' . config('app.name'));
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
            'type' => 'absence_eleve',
            'absence_id' => $this->absence->id,
            'eleve_id' => $this->eleve->id,
            'message' => 'Votre enfant ' . $this->eleve->prenom . ' ' . $this->eleve->nom . 
                         ' a été marqué(e) absent(e) le ' . $this->absence->date_absence->format('d/m/Y') . 
                         ' en ' . $this->absence->matiere->nom,
            'url' => route('parent.absences.show', $this->absence->id)
        ];
    }
}
