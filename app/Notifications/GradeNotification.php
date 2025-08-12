<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class GradeNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The grade instance.
     *
     * @var \App\Models\Grade
     */
    public $grade;

    /**
     * The notification type (new_grade, updated_grade, low_grade, etc.).
     *
     * @var string
     */
    public $notificationType;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Grade  $grade
     * @param  string  $type
     * @return void
     */
    public function __construct(Grade $grade, string $type = 'new_grade')
    {
        parent::__construct();
        
        $this->grade = $grade->load('evaluation', 'student', 'evaluation.course');
        $this->notificationType = $type;
        $this->type = $this->getNotificationType();
        $this->icon = $this->getNotificationIcon();
        
        // Définir le contenu de la notification
        $this->setContent();
    }

    /**
     * Get the notification type based on notification type.
     *
     * @return string
     */
    protected function getNotificationType()
    {
        if ($this->notificationType === 'low_grade') {
            return 'warning';
        }
        
        return $this->grade->grade >= ($this->grade->evaluation->passing_grade ?? 10) ? 'success' : 'warning';
    }

    /**
     * Get the notification icon based on notification type.
     *
     * @return string
     */
    protected function getNotificationIcon()
    {
        if ($this->notificationType === 'low_grade') {
            return 'exclamation-triangle';
        }
        
        return $this->grade->grade >= ($this->grade->evaluation->passing_grade ?? 10) ? 'check-circle' : 'exclamation-circle';
    }

    /**
     * Set the notification content.
     *
     * @return void
     */
    protected function setContent()
    {
        $studentName = $this->grade->student->full_name;
        $courseName = $this->grade->evaluation->course->name;
        $evaluationName = $this->grade->evaluation->name;
        $gradeValue = number_format($this->grade->grade, 2, ',', ' ');
        $maxGrade = $this->grade->evaluation->max_grade;
        $passingGrade = $this->grade->evaluation->passing_grade ?? 10;
        $evaluationDate = $this->grade->evaluation->evaluation_date->format('d/m/Y');
        
        switch ($this->notificationType) {
            case 'new_grade':
                $this->title = "Nouvelle note enregistrée";
                $this->message = "Une nouvelle note a été enregistrée pour $studentName en $courseName.\nÉvaluation: $evaluationName\nNote: $gradeValue/$maxGrade";
                $this->actionUrl = route('grades.show', $this->grade->id);
                $this->actionText = 'Voir les détails';
                break;
                
            case 'updated_grade':
                $this->title = "Note mise à jour";
                $this->message = "La note de $studentName pour l'évaluation $evaluationName a été mise à jour.\nNouvelle note: $gradeValue/$maxGrade";
                $this->actionUrl = route('grades.show', $this->grade->id);
                $this->actionText = 'Voir les détails';
                break;
                
            case 'low_grade':
                $this->title = "Note en dessous de la moyenne";
                $this->message = "Attention : $studentName a obtenu une note de $gradeValue/$maxGrade à l'évaluation $evaluationName, ce qui est en dessous de la moyenne requise ($passingGrade/$maxGrade).";
                $this->actionUrl = route('students.grades', $this->grade->student_id);
                $this->actionText = 'Voir toutes les notes';
                break;
        }
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
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->message);
            
        if ($this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }
        
        // Ajouter des détails supplémentaires pour les notes
        $mail->line('')
             ->line('Détails de l\'évaluation:')
             ->line('- Cours: ' . $this->grade->evaluation->course->name)
             ->line('- Type: ' . ucfirst($this->grade->evaluation->type))
             ->line('- Date: ' . $this->grade->evaluation->evaluation_date->format('d/m/Y'))
             ->line('- Note obtenue: ' . number_format($this->grade->grade, 2, ',', ' ') . '/' . $this->grade->evaluation->max_grade)
             ->line('- Note de passage: ' . ($this->grade->evaluation->passing_grade ?? 'Non définie'));
        
        // Ajouter un message personnalisé pour les notes basses
        if ($this->type === 'warning') {
            $mail->line('')
                 ->line('Nous vous encourageons à consulter les ressources supplémentaires et à contacter le professeur si nécessaire.');
        }
        
        return $mail->line('')
                   ->line('Cordialement,')
                   ->line(config('app.name'));
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
            'grade_id' => $this->grade->id,
            'student_id' => $this->grade->student_id,
            'evaluation_id' => $this->grade->evaluation_id,
            'course_id' => $this->grade->evaluation->course_id,
            'grade_value' => $this->grade->grade,
            'max_grade' => $this->grade->evaluation->max_grade,
            'passing_grade' => $this->grade->evaluation->passing_grade,
            'notification_type' => $this->notificationType,
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
