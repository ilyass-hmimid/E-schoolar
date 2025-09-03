<?php

namespace App\Notifications;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceEleveProfesseur extends Notification implements ShouldQueue
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
                    ->subject('Nouvelle absence - ' . $this->eleve->prenom . ' ' . $this->eleve->nom)
                    ->greeting('Bonjour ' . $notifiable->prenom . ',')
                    ->line('Un élève de votre classe a été marqué absent pour votre cours.')
                    ->line('Détails de l\'absence :')
                    ->line('- Élève: ' . $this->eleve->prenom . ' ' . $this->eleve->nom)
                    ->line('- Classe: ' . ($this->eleve->classe->nom ?? 'Non défini'))
                    ->line('- Matière: ' . $this->absence->matiere->nom)
                    ->line('- Date: ' . $this->absence->date_absence->format('d/m/Y'))
                    ->line('- Période: De ' . $this->absence->heure_debut . ' à ' . $this->absence->heure_fin)
                    ->line('- Type: ' . ucfirst($this->absence->type))
                    ->line('- Motif: ' . ($this->absence->motif ?? 'Non précisé'))
                    ->action('Voir le détail', route('professeur.absences.show', $this->absence->id))
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
            'type' => 'absence_eleve_professeur',
            'absence_id' => $this->absence->id,
            'eleve_id' => $this->eleve->id,
            'message' => $this->eleve->prenom . ' ' . $this->eleve->nom . 
                         ' a été marqué(e) absent(e) pour votre cours du ' . 
                         $this->absence->date_absence->format('d/m/Y') . ' en ' . 
                         $this->absence->matiere->nom,
            'url' => route('professeur.absences.show', $this->absence->id)
        ];
    }
}
