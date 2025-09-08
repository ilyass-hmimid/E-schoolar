<?php

namespace App\Notifications;

use App\Models\Salaire;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SalairePaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Le salaire concerné
     *
     * @var \App\Models\Salaire
     */
    public $salaire;

    /**
     * Créer une nouvelle instance de notification
     *
     * @param  \App\Models\Salaire  $salaire
     * @return void
     */
    public function __construct(Salaire $salaire)
    {
        $this->salaire = $salaire;
    }

    /**
     * Obtenir les canaux de diffusion de la notification
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Obtenir la représentation mail de la notification
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $periode = \Carbon\Carbon::parse($this->salaire->periode)->format('F Y');
        $montant = number_format($this->salaire->salaire_net, 2, ',', ' ');
        $datePaiement = \Carbon\Carbon::parse($this->salaire->date_paiement)->format('d/m/Y');
        
        return (new MailMessage)
                    ->subject("Paiement de votre salaire - $periode")
                    ->greeting('Bonjour ' . $notifiable->prenom . ',')
                    ->line('Nous vous informons que votre salaire pour la période de ' . $periode . ' a été payé avec succès.')
                    ->line("**Montant net:** $montant DH")
                    ->line("**Date de paiement:** " . $datePaiement)
                    ->line("**Mode de paiement:** " . $this->getTypePaiementLabel())
                    ->line('Pour toute question concernant ce paiement, veuillez contacter le service comptable.')
                    ->action('Voir les détails', route('professeur.salaires.show', $this->salaire))
                    ->line('Merci d\'utiliser notre plateforme !');
    }

    /**
     * Obtenir la représentation sous forme de tableau de la notification
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'titre' => 'Paiement de salaire reçu',
            'message' => 'Votre salaire pour ' . \Carbon\Carbon::parse($this->salaire->periode)->format('F Y') . ' a été payé.',
            'lien' => route('professeur.salaires.show', $this->salaire),
            'type' => 'salaire_paye',
            'salaire_id' => $this->salaire->id,
            'montant' => $this->salaire->salaire_net,
            'date_paiement' => \Carbon\Carbon::parse($this->salaire->date_paiement)->toDateString(),
        ];
    }
    
    /**
     * Obtenir le libellé du type de paiement
     *
     * @return string
     */
    protected function getTypePaiementLabel()
    {
        $types = [
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
        ];
        
        return $types[$this->salaire->type_paiement] ?? $this->salaire->type_paiement;
    }
}
