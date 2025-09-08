<?php

namespace App\Notifications;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class AbsenceCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * L'absence concernée
     *
     * @var \App\Models\Absence
     */
    protected $absence;

    /**
     * Le rôle du destinataire
     *
     * @var string
     */
    protected $recipientRole;

    /**
     * Crée une nouvelle instance de notification.
     *
     * @param \App\Models\Absence $absence
     * @param string $recipientRole
     */
    public function __construct(Absence $absence, string $recipientRole)
    {
        $this->absence = $absence->load(['eleve', 'professeur', 'matiere']);
        $this->recipientRole = $recipientRole;
    }

    /**
     * Détermine les canaux de diffusion de la notification.
     *
     * @param  mixed  $notifiable
     * @return array<string>
     */
    public function via($notifiable): array
    {
        // Par défaut, on utilise la base de données et le mail
        $channels = ['database'];
        
        // Si l'utilisateur a une adresse email, on ajoute le canal mail
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Obtient la représentation mail de la notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = '';
        $greeting = '';
        $lines = [];
        
        // Personnalisation du message en fonction du rôle du destinataire
        switch ($this->recipientRole) {
            case 'Élève':
                $subject = 'Nouvelle absence enregistrée';
                $greeting = 'Bonjour ' . $notifiable->name . ',';
                $lines = [
                    'Une absence a été enregistrée à votre nom pour le cours de ' . $this->absence->matiere->nom . '.',
                    'Date: ' . Carbon::parse($this->absence->date_absence)->format('d/m/Y'),
                    'Heure: de ' . $this->absence->heure_debut . ' à ' . $this->absence->heure_fin,
                    'Professeur: ' . $this->absence->professeur->name,
                    'Statut: ' . $this->getStatusLabel(),
                    '',
                    'Motif: ' . ($this->absence->motif ?: 'Aucun motif spécifié'),
                ];
                break;
                
            case 'Professeur':
                $subject = 'Nouvelle absence enregistrée pour un élève';
                $greeting = 'Bonjour ' . $notifiable->name . ',';
                $lines = [
                    'Une absence a été enregistrée pour l\'élève ' . $this->absence->eleve->name . ' pour votre cours de ' . $this->absence->matiere->nom . '.',
                    'Date: ' . Carbon::parse($this->absence->date_absence)->format('d/m/Y'),
                    'Heure: de ' . $this->absence->heure_debut . ' à ' . $this->absence->heure_fin,
                    'Statut: ' . $this->getStatusLabel(),
                    '',
                    'Motif: ' . ($this->absence->motif ?: 'Aucun motif spécifié'),
                ];
                break;
                
            case 'Administrateur':
            default:
                $subject = 'Nouvelle absence enregistrée dans le système';
                $greeting = 'Bonjour,';
                $lines = [
                    'Une nouvelle absence a été enregistrée dans le système.',
                    'Élève: ' . $this->absence->eleve->name,
                    'Matière: ' . $this->absence->matiere->nom,
                    'Professeur: ' . $this->absence->professeur->name,
                    'Date: ' . Carbon::parse($this->absence->date_absence)->format('d/m/Y'),
                    'Heure: de ' . $this->absence->heure_debut . ' à ' . $this->absence->heure_fin,
                    'Statut: ' . $this->getStatusLabel(),
                    '',
                    'Motif: ' . ($this->absence->motif ?: 'Aucun motif spécifié'),
                ];
                break;
        }
        
        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting);
            
        foreach ($lines as $line) {
            $mail->line($line);
        }
        
        // Ajout d'un bouton pour voir les détails de l'absence
        $mail->action('Voir les détails', url('/admin/absences/' . $this->absence->id));
        
        return $mail;
    }
    
    /**
     * Obtient le libellé du statut de l'absence
     *
     * @return string
     */
    protected function getStatusLabel()
    {
        $statuses = [
            'non_justifiee' => 'Non justifiée',
            'en_attente' => 'En attente de justification',
            'justifiee' => 'Justifiée',
        ];
        
        return $statuses[$this->absence->statut] ?? $this->absence->statut;
    }

    /**
     * Obtient la représentation de la notification pour la base de données.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'absence_id' => $this->absence->id,
            'eleve_id' => $this->absence->eleve_id,
            'professeur_id' => $this->absence->professeur_id,
            'matiere_id' => $this->absence->matiere_id,
            'date_absence' => Carbon::parse($this->absence->date_absence)->format('Y-m-d'),
            'statut' => $this->absence->statut,
            'message' => $this->getNotificationMessage(),
            'url' => url('/admin/absences/' . $this->absence->id),
        ];
    }
    
    /**
     * Génère le message de notification en fonction du rôle du destinataire
     *
     * @return string
     */
    protected function getNotificationMessage()
    {
        switch ($this->recipientRole) {
            case 'Élève':
                return 'Une absence a été enregistrée pour le cours de ' . $this->absence->matiere->nom . ' du ' . Carbon::parse($this->absence->date_absence)->format('d/m/Y');
                
            case 'Professeur':
                return 'Une absence a été enregistrée pour ' . $this->absence->eleve->name . ' pour le cours de ' . $this->absence->matiere->nom;
                
            case 'Administrateur':
            default:
                return 'Nouvelle absence enregistrée pour ' . $this->absence->eleve->name . ' - ' . $this->absence->matiere->nom;
        }
    }
    
    /**
     * Obtient la représentation tableau de la notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->getNotificationMessage(),
            'url' => url('/admin/absences/' . $this->absence->id),
            'icon' => 'fa-user-times',
            'color' => 'warning',
        ];
    }
}
