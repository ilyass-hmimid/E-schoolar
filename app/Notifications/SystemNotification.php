<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SystemNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Additional data for the notification.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new notification instance.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  array  $data
     * @return void
     */
    public function __construct(
        string $title = null, 
        string $message = null, 
        string $type = 'info',
        array $data = []
    ) {
        parent::__construct($title, $message, $type, $data);
        
        $this->data = $data;
        $this->type = $type;
        $this->icon = $this->getNotificationIcon();
        
        // Définir l'URL d'action si fournie dans les données
        if (isset($data['action_url'])) {
            $this->actionUrl = $data['action_url'];
        }
        
        if (isset($data['action_text'])) {
            $this->actionText = $data['action_text'];
        }
    }

    /**
     * Get the notification icon based on type.
     *
     * @return string
     */
    protected function getNotificationIcon()
    {
        return match($this->type) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'error' => 'times-circle',
            'info' => 'info-circle',
            'announcement' => 'bullhorn',
            'update' => 'sync',
            default => 'bell',
        };
    }

    /**
     * Create a success notification.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $data
     * @return static
     */
    public static function success(string $title, string $message, array $data = [])
    {
        return new static($title, $message, 'success', $data);
    }

    /**
     * Create a warning notification.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $data
     * @return static
     */
    public static function warning(string $title, string $message, array $data = [])
    {
        return new static($title, $message, 'warning', $data);
    }

    /**
     * Create an error notification.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $data
     * @return static
     */
    public static function error(string $title, string $message, array $data = [])
    {
        return new static($title, $message, 'error', $data);
    }

    /**
     * Create an info notification.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $data
     * @return static
     */
    public static function info(string $title, string $message, array $data = [])
    {
        return new static($title, $message, 'info', $data);
    }

    /**
     * Create an announcement notification.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $data
     * @return static
     */
    public static function announcement(string $title, string $message, array $data = [])
    {
        return new static($title, $message, 'announcement', $data);
    }

    /**
     * Create an update notification.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $data
     * @return static
     */
    public static function update(string $title, string $message, array $data = [])
    {
        return new static($title, $message, 'update', $data);
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
            ->subject($this->getTitle())
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->getMessage());
            
        if ($this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }
        
        // Ajouter des données supplémentaires si présentes
        if (!empty($this->data['details'])) {
            $mail->line('')
                 ->line('Détails supplémentaires :');
                 
            if (is_array($this->data['details'])) {
                foreach ($this->data['details'] as $label => $value) {
                    $mail->line("- {$label}: {$value}");
                }
            } else {
                $mail->line($this->data['details']);
            }
        }
        
        // Ajouter une note de bas de page si fournie
        if (!empty($this->data['footer'])) {
            $mail->line('')
                 ->line($this->data['footer']);
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
            'type' => $this->type,
            'data' => $this->data,
        ]);
    }
}
