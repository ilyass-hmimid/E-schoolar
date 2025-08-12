<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notification's delivery channels.
     *
     * @var array
     */
    public $channels = [];

    /**
     * The notification's type.
     *
     * @var string
     */
    public $type = 'info';

    /**
     * The notification's icon.
     *
     * @var string
     */
    public $icon = 'info-circle';

    /**
     * The notification's title.
     *
     * @var string
     */
    public $title;

    /**
     * The notification's message.
     *
     * @var string
     */
    public $message;

    /**
     * The notification's action URL.
     *
     * @var string|null
     */
    public $actionUrl;

    /**
     * The notification's action text.
     *
     * @var string
     */
    public $actionText = 'Voir les détails';

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
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->channels = $this->getChannels();
        
        // Set additional properties from data array
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
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
        // Vérifier si des canaux sont définis dans la configuration
        $channels = $this->channels ?: ['database'];
        
        // Si l'utilisateur a une préférence, l'utiliser
        if (method_exists($notifiable, 'preferredNotificationChannels')) {
            $userChannels = $notifiable->preferredNotificationChannels($this);
            if (!empty($userChannels)) {
                $channels = $userChannels;
            }
        }
        
        // Filtrer les canaux non activés dans la configuration
        return array_filter($channels, function ($channel) {
            return config("notifications.channels.{$channel}", true);
        });
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
            ->line(new HtmlString(nl2br(e($this->getMessage()))));

        if ($this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        $mail->line('Merci d\'utiliser notre application!');

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
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'type' => $this->type,
            'icon' => $this->icon,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the notification's title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?: class_basename(static::class);
    }

    /**
     * Get the notification's message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message ?: 'Vous avez une nouvelle notification.';
    }

    /**
     * Get the notification's channels.
     *
     * @return array
     */
    protected function getChannels()
    {
        return ['database'];
    }

    /**
     * Set the notification's channels.
     *
     * @param  array  $channels
     * @return $this
     */
    public function viaChannels(array $channels)
    {
        $this->channels = $channels;
        return $this;
    }
}
