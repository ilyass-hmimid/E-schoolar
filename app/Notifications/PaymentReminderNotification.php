<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class PaymentReminderNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The payment amount.
     *
     * @var float
     */
    public $amount;

    /**
     * The due date.
     *
     * @var string
     */
    public $dueDate;

    /**
     * The payment type (tuition, registration, etc.).
     *
     * @var string
     */
    public $paymentType;

    /**
     * The number of days until/since due.
     *
     * @var int
     */
    public $daysCount;

    /**
     * Whether the payment is overdue.
     *
     * @var bool
     */
    public $isOverdue;

    /**
     * Create a new notification instance.
     *
     * @param  float  $amount
     * @param  string  $dueDate
     * @param  string  $paymentType
     * @param  int  $daysCount
     * @param  bool  $isOverdue
     * @return void
     */
    public function __construct(
        float $amount, 
        string $dueDate, 
        string $paymentType = 'frais de scolarité',
        int $daysCount = 0,
        bool $isOverdue = false
    ) {
        parent::__construct();
        
        $this->amount = $amount;
        $this->dueDate = $dueDate;
        $this->paymentType = $paymentType;
        $this->daysCount = $daysCount;
        $this->isOverdue = $isOverdue;
        
        $this->type = $isOverdue ? 'warning' : 'info';
        $this->icon = $isOverdue ? 'exclamation-triangle' : 'bell';
        
        $this->setContent();
    }

    /**
     * Set the notification content.
     *
     * @return void
     */
    protected function setContent()
    {
        $formattedAmount = number_format($this->amount, 2, ',', ' ') . ' ' . config('app.currency', 'MAD');
        
        if ($this->isOverdue) {
            $this->title = "Paiement en retard";
            $this->message = "Votre paiement de $formattedAmount pour $this->paymentType était dû le $this->dueDate (il y a $this->daysCount jour(s)). Veuillez régulariser votre situation dès que possible pour éviter tout désagrément.";
            $this->actionText = 'Effectuer le paiement maintenant';
        } else {
            if ($this->daysCount > 0) {
                $this->title = "Rappel de paiement";
                $this->message = "Un paiement de $formattedAmount pour $this->paymentType est dû dans $this->daysCount jour(s) (le $this->dueDate).";
                $this->actionText = 'Payer maintenant';
            } else {
                $this->title = "Paiement dû aujourd'hui";
                $this->message = "Un paiement de $formattedAmount pour $this->paymentType est dû aujourd'hui (le $this->dueDate).";
                $this->actionText = 'Effectuer le paiement';
            }
        }
        
        $this->actionUrl = route('payments.create');
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
            ->greeting('Bonjour ' . $notifiable->name . ',');
            
        $mail->line($this->message);
        
        if ($this->isOverdue) {
            $mail->line('')
                 ->line('**Attention** : Des frais de retard pourraient être appliqués si le paiement n\'est pas effectué rapidement.');
        }
        
        $mail->line('')
             ->line('Détails du paiement :')
             ->line(new HtmlString("- **Montant :** {$this->amount} " . config('app.currency', 'MAD')))
             ->line(new HtmlString("- Type :** " . ucfirst($this->paymentType)))
             ->line(new HtmlString("- Date d'échéance :** $this->dueDate"));
        
        if ($this->isOverdue) {
            $mail->line(new HtmlString("- Jours de retard :** $this->daysCount"));
        }
        
        $mail->line('')
             ->line('Vous pouvez effectuer votre paiement en ligne en cliquant sur le bouton ci-dessous :')
             ->action($this->actionText, $this->actionUrl)
             ->line('')
             ->line('Méthodes de paiement acceptées :')
             ->line('- Carte bancaire')
             ->line('- Virement bancaire')
             ->line('- Espèces (uniquement à l\'accueil)')
             ->line('- Chèque (à l\'ordre de ' . config('app.name') . ')')
             ->line('')
             ->line('Pour toute question concernant ce paiement, n\'hésitez pas à contacter notre service comptable.')
             ->line('')
             ->line('Cordialement,')
             ->line('L\'équipe ' . config('app.name'));
        
        return $mail;
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
            'type' => 'payment_reminder',
            'amount' => $this->amount,
            'due_date' => $this->dueDate,
            'payment_type' => $this->paymentType,
            'days_count' => $this->daysCount,
            'is_overdue' => $this->isOverdue,
            'currency' => config('app.currency', 'MAD'),
        ]);
    }
    
    /**
     * Create a payment due soon notification.
     *
     * @param  float  $amount
     * @param  string  $dueDate
     * @param  string  $paymentType
     * @param  int  $daysUntilDue
     * @return static
     */
    public static function dueSoon(float $amount, string $dueDate, string $paymentType = 'frais de scolarité', int $daysUntilDue = 7)
    {
        return new static($amount, $dueDate, $paymentType, $daysUntilDue, false);
    }
    
    /**
     * Create an overdue payment notification.
     *
     * @param  float  $amount
     * @param  string  $dueDate
     * @param  string  $paymentType
     * @param  int  $daysOverdue
     * @return static
     */
    public static function overdue(float $amount, string $dueDate, string $paymentType = 'frais de scolarité', int $daysOverdue = 1)
    {
        return new static($amount, $dueDate, $paymentType, $daysOverdue, true);
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
