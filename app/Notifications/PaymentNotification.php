<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The payment instance.
     *
     * @var \App\Models\Payment
     */
    public $payment;

    /**
     * The notification type (confirmation, reminder, late, etc.).
     *
     * @var string
     */
    public $notificationType;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Payment  $payment
     * @param  string  $type
     * @return void
     */
    public function __construct(Payment $payment, string $type = 'confirmation')
    {
        parent::__construct();
        
        $this->payment = $payment->load('student', 'paymentMethod');
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
        return match($this->notificationType) {
            'confirmation' => 'success',
            'reminder' => 'info',
            'late' => 'warning',
            'cancelled' => 'danger',
            default => 'info',
        };
    }

    /**
     * Get the notification icon based on notification type.
     *
     * @return string
     */
    protected function getNotificationIcon()
    {
        return match($this->notificationType) {
            'confirmation' => 'check-circle',
            'reminder' => 'bell',
            'late' => 'exclamation-triangle',
            'cancelled' => 'times-circle',
            default => 'info-circle',
        };
    }

    /**
     * Set the notification content.
     *
     * @return void
     */
    protected function setContent()
    {
        $studentName = $this->payment->student->full_name;
        $amount = number_format($this->payment->amount, 2, ',', ' ') . ' ' . config('app.currency', 'MAD');
        $date = $this->payment->payment_date->format('d/m/Y');
        $method = $this->payment->paymentMethod->name ?? 'Non spécifié';
        
        switch ($this->notificationType) {
            case 'confirmation':
                $this->title = "Paiement confirmé";
                $this->message = "Votre paiement de $amount pour $studentName a été enregistré avec succès le $date.\nMéthode: $method";
                $this->actionUrl = route('payments.show', $this->payment->id);
                $this->actionText = 'Télécharger le reçu';
                break;
                
            case 'reminder':
                $dueDate = $this->payment->due_date->format('d/m/Y');
                $this->title = "Rappel de paiement";
                $this->message = "Rappel : Un paiement de $amount pour $studentName est dû pour le $dueDate.";
                $this->actionUrl = route('payments.create', ['student_id' => $this->payment->student_id]);
                $this->actionText = 'Effectuer le paiement';
                break;
                
            case 'late':
                $daysLate = now()->diffInDays($this->payment->due_date);
                $this->title = "Paiement en retard";
                $this->message = "Le paiement de $amount pour $studentName est en retard de $daysLate jour(s). Des frais de retard pourraient s'appliquer.";
                $this->actionUrl = route('payments.create', ['student_id' => $this->payment->student_id]);
                $this->actionText = 'Régulariser la situation';
                break;
                
            case 'cancelled':
                $this->title = "Paiement annulé";
                $this->message = "Votre paiement de $amount pour $studentName a été annulé. Veuillez contacter le support pour plus d'informations.";
                $this->actionUrl = route('support');
                $this->actionText = 'Contacter le support';
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
        
        // Ajouter des détails supplémentaires pour les paiements
        $mail->line('')
             ->line('Détails du paiement:')
             ->line('- Référence: ' . $this->payment->reference)
             ->line('- Montant: ' . number_format($this->payment->amount, 2, ',', ' ') . ' ' . config('app.currency', 'MAD'))
             ->line('- Date: ' . $this->payment->payment_date->format('d/m/Y'))
             ->line('- Méthode: ' . ($this->payment->paymentMethod->name ?? 'Non spécifiée'))
             ->line('- Statut: ' . ucfirst($this->payment->status));
        
        if ($this->payment->notes) {
            $mail->line('')
                 ->line('Notes:')
                 ->line($this->payment->notes);
        }
        
        return $mail->line('')
                   ->line('Merci de votre confiance.');
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
            'payment_id' => $this->payment->id,
            'student_id' => $this->payment->student_id,
            'amount' => $this->payment->amount,
            'currency' => config('app.currency', 'MAD'),
            'payment_date' => $this->payment->payment_date->toDateString(),
            'due_date' => $this->payment->due_date ? $this->payment->due_date->toDateString() : null,
            'status' => $this->payment->status,
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
