<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class WelcomeNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The user's temporary password (if any).
     *
     * @var string|null
     */
    public $password;

    /**
     * The verification token (if email verification is required).
     *
     * @var string|null
     */
    public $verificationToken;

    /**
     * Create a new notification instance.
     *
     * @param  string|null  $password
     * @param  string|null  $verificationToken
     * @return void
     */
    public function __construct(?string $password = null, ?string $verificationToken = null)
    {
        parent::__construct();
        
        $this->password = $password;
        $this->verificationToken = $verificationToken;
        $this->type = 'success';
        $this->icon = 'user-plus';
        $this->title = 'Bienvenue sur ' . config('app.name');
        $this->actionText = 'Accéder à mon compte';
        $this->actionUrl = getDashboardUrl();
        
        $this->setWelcomeMessage();
    }

    /**
     * Set the welcome message based on the context.
     *
     * @return void
     */
    protected function setWelcomeMessage()
    {
        $appName = config('app.name');
        $message = "Bienvenue sur $appName ! Nous sommes ravis de vous compter parmi nous. ";
        
        if ($this->password) {
            $message .= "Votre compte a été créé avec succès. ";
        } else {
            $message .= "Votre compte a été créé avec succès. ";
        }
        
        $message .= "Vous pouvez dès maintenant accéder à toutes les fonctionnalités de la plateforme.";
        
        $this->message = $message;
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
            ->subject('Bienvenue sur ' . config('app.name'))
            ->greeting('Bienvenue ' . $notifiable->name . ' !')
            ->line('Nous sommes ravis de vous accueillir sur notre plateforme.');
        
        // Ajouter les informations de connexion si un mot de passe est fourni
        if ($this->password) {
            $mail->line('Voici vos informations de connexion :')
                 ->line(new HtmlString("<strong>Email:</strong> {$notifiable->email}"))
                 ->line(new HtmlString("<strong>Mot de passe temporaire:</strong> {$this->password}"));
            
            $mail->line('Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe dès votre première connexion.');
        }
        
        // Ajouter le lien de vérification d'email si nécessaire
        if ($this->verificationToken) {
            $verificationUrl = route('verification.verify', [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]);
            
            $mail->line('Veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous :')
                 ->action('Vérifier mon email', $verificationUrl);
        } else {
            $mail->action('Se connecter', route('login'));
        }
        
        // Ajouter des informations supplémentaires
        $mail->line('')
             ->line('Avec votre compte, vous pouvez :')
             ->line('- Consulter votre emploi du temps')
             ->line('- Accéder à vos cours et supports de formation')
             ->line('- Suivre votre progression')
             ->line('- Prendre rendez-vous avec vos professeurs')
             ->line('- Et bien plus encore !')
             ->line('')
             ->line('Si vous avez des questions, n\'hésitez pas à contacter notre équipe de support.')
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
            'type' => 'welcome',
            'has_password' => !is_null($this->password),
            'requires_verification' => !is_null($this->verificationToken),
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
