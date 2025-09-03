<?php

namespace App\Notifications;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JustificationAbsenceTraitee extends Notification implements ShouldQueue
{
    use Queueable;

    protected $absence;
    protected $statut;
    protected $eleve;

    /**
     * Create a new notification instance.
     *
     * @param Absence $absence
     * @param string $statut
     * @param User|null $eleve
     * @return void
     */
    public function __construct(Absence $absence, string $statut, ?User $eleve = null)
    {
        $this->absence = $absence;
        $this->statut = $statut;
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
        $mail = (new MailMessage)
            ->subject('Traitement de votre justification d\'absence')
            ->greeting('Bonjour ' . $notifiable->prenom . ',');

        if ($this->eleve) {
            // Notification pour un parent
            $mail->line('La justification d\'absence de votre enfant ' . $this->eleve->prenom . ' ' . $this->eleve->nom . ' a été ' . $this->statut . '.');
        } else {
            // Notification pour l'étudiant
            $mail->line('Votre justification d\'absence a été ' . $this->statut . '.');
        }

        $mail->line('Détails de l\'absence :')
            ->line('- Matière: ' . $this->absence->matiere->nom)
            ->line('- Date: ' . $this->absence->date_absence->format('d/m/Y'))
            ->line('- Période: De ' . $this->absence->heure_debut . ' à ' . $this->absence->heure_fin)
            ->line('- Statut: ' . ucfirst($this->statut));

        if ($this->absence->commentaire_validation) {
            $mail->line('Commentaire: ' . $this->absence->commentaire_validation);
        }

        $route = $this->eleve ? 'parent.absences.show' : 'mes-absences.show';
        
        return $mail->action('Voir le détail', route($route, $this->absence->id))
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
        $message = $this->eleve 
            ? 'La justification d\'absence de ' . $this->eleve->prenom . ' ' . $this->eleve->nom . ' a été ' . $this->statut
            : 'Votre justification d\'absence a été ' . $this->statut;

        $url = $this->eleve 
            ? route('parent.absences.show', $this->absence->id)
            : route('mes-absences.show', $this->absence->id);

        return [
            'type' => 'justification_traitee',
            'absence_id' => $this->absence->id,
            'eleve_id' => $this->eleve ? $this->eleve->id : null,
            'statut' => $this->statut,
            'message' => $message,
            'url' => $url
        ];
    }
}
