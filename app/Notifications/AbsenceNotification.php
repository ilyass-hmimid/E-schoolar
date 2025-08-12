<?php

namespace App\Notifications;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AbsenceNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The absence instance.
     *
     * @var \App\Models\Absence
     */
    public $absence;

    /**
     * The recipient type (admin, teacher, parent).
     *
     * @var string
     */
    public $recipientType;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Absence  $absence
     * @param  string  $recipientType
     * @return void
     */
    public function __construct(Absence $absence, string $recipientType = 'admin')
    {
        parent::__construct();
        
        $this->absence = $absence;
        $this->recipientType = $recipientType;
        $this->type = 'warning';
        $this->icon = 'user-times';
        
        // Définir le titre et le message en fonction du type de destinataire
        $this->setContentForRecipient();
    }

    /**
     * Set the notification content based on recipient type.
     *
     * @return void
     */
    protected function setContentForRecipient()
    {
        $studentName = $this->absence->student->full_name;
        $courseName = $this->absence->course->name ?? 'un cours';
        $date = $this->absence->date->format('d/m/Y');
        $time = $this->absence->start_time->format('H:i');
        
        switch ($this->recipientType) {
            case 'admin':
                $this->title = "Nouvelle absence enregistrée";
                $this->message = "L'étudiant $studentName était absent pour $courseName le $date à $time.";
                $this->actionUrl = route('admin.absences.show', $this->absence->id);
                $this->actionText = 'Voir les détails de l\'absence';
                break;
                
            case 'teacher':
                $this->title = "Absence dans votre cours";
                $this->message = "$studentName était absent pour votre cours de $courseName le $date à $time.";
                $this->actionUrl = route('teacher.absences.show', $this->absence->id);
                $this->actionText = 'Voir les détails';
                break;
                
            case 'parent':
                $this->title = "Absence de votre enfant";
                $this->message = "Votre enfant $studentName était absent pour $courseName le $date à $time.";
                $this->actionUrl = route('parent.absences.show', $this->absence->id);
                $this->actionText = 'Voir les détails';
                break;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Utiliser les canaux par défaut définis dans la configuration
        return parent::via($notifiable);
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
            ->subject($this->title)
            ->line($this->message);
            
        if ($this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }
        
        // Ajouter des informations supplémentaires pour les administrateurs
        if ($this->recipientType === 'admin') {
            $mail->line('')
                 ->line('Détails supplémentaires:')
                 ->line('- Étudiant: ' . $this->absence->student->full_name)
                 ->line('- Classe: ' . ($this->absence->student->classGroup->name ?? 'Non défini'))
                 ->line('- Cours: ' . ($this->absence->course->name ?? 'Non défini'))
                 ->line('- Date: ' . $this->absence->date->format('d/m/Y'))
                 ->line('- Période: ' . $this->absence->start_time->format('H:i') . ' - ' . $this->absence->end_time->format('H:i'));
        }
        
        return $mail->line('')
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
        return array_merge(parent::toArray($notifiable), [
            'absence_id' => $this->absence->id,
            'student_id' => $this->absence->student_id,
            'course_id' => $this->absence->course_id,
            'date' => $this->absence->date->toDateString(),
            'start_time' => $this->absence->start_time->toTimeString(),
            'end_time' => $this->absence->end_time->toTimeString(),
            'reason' => $this->absence->reason,
            'recipient_type' => $this->recipientType,
        ]);
    }
    
    /**
     * Get the notification's title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Get the notification's message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
