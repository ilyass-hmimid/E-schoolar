# Système de Notifications

Ce document décrit le système de notifications complet mis en place pour l'application Allo Tawjih. Ce système permet d'envoyer, de gérer et d'afficher des notifications aux utilisateurs via différents canaux (email, notification interne, push, SMS).

## Table des matières

1. [Architecture du système](#architecture-du-système)
2. [Configuration](#configuration)
3. [Utilisation côté backend](#utilisation-côté-backend)
   - [Création d'une notification](#création-dune-notification)
   - [Types de notifications intégrés](#types-de-notifications-intégrés)
   - [Envoi de notifications](#envoi-de-notifications)
4. [Utilisation côté frontend](#utilisation-côté-frontend)
   - [Composants disponibles](#composants-disponibles)
   - [Service de notifications](#service-de-notifications)
   - [Notifications en temps réel](#notifications-en-temps-réel)
5. [Personnalisation](#personnalisation)
   - [Créer un nouveau type de notification](#créer-un-nouveau-type-de-notification)
   - [Ajouter un canal de notification](#ajouter-un-canal-de-notification)
6. [Tests](#tests)
7. [Dépannage](#dépannage)

## Architecture du système

Le système de notifications est construit autour de plusieurs composants clés :

- **Backend (Laravel)**:
  - Modèle `Notification` (hérite de `Illuminate\Notifications\Notification`)
  - Contrôleur `NotificationController` pour l'API
  - Services de notification (`NotificationService`, `RealtimeNotificationService`)
  - Événements et écouteurs pour les notifications en temps réel

- **Frontend (Vue.js)**:
  - Composants Vue (`NotificationBell`, `NotificationPreferencesModal`, `ToastNotifications`)
  - Services JavaScript (`NotificationService`, `RealtimeNotificationService`)
  - Plugin Vue pour une intégration facile
  - Configuration centralisée

- **Stockage**:
  - Base de données pour les notifications internes
  - Fichiers de configuration pour les préférences et les modèles

## Configuration

### Configuration du backend

Le fichier `config/notifications.php` contient la configuration de base des notifications :

```php
return [
    // Activer/désactiver le système de notifications
    'enabled' => true,
    
    // Canaux de notification disponibles
    'channels' => [
        'mail',
        'database',
        'broadcast',
        // Autres canaux...
    ],
    
    // Configuration des canaux
    'mail' => [
        // Configuration spécifique à l'email
    ],
    
    // Paramètres des notifications en temps réel
    'broadcasting' => [
        'enabled' => env('BROADCAST_CONNECTION', 'pusher') !== null,
        'connection' => env('BROADCAST_CONNECTION', 'pusher'),
    ],
];
```

### Configuration du frontend

Le fichier `resources/js/config/notifications.js` contient la configuration côté client :

```javascript
export default {
  // Paramètres généraux
  general: {
    enabled: true,
    toastDuration: 5000,
    maxVisibleNotifications: 15,
    refreshInterval: 30000,
  },
  
  // Configuration des canaux et types de notifications...
};
```

## Utilisation côté backend

### Création d'une notification

Pour créer une nouvelle notification, utilisez la commande Artisan :

```bash
php artisan make:notification NomDeLaNotification
```

Exemple de notification personnalisée :

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->data['titre'])
                    ->line($this->data['message'])
                    ->action('Voir', $this->data['url'])
                    ->line('Merci d\'utiliser notre application!');
    }

    public function toArray($notifiable)
    {
        return [
            'titre' => $this->data['titre'],
            'message' => $this->data['message'],
            'url' => $this->data['url'],
            'type' => $this->data['type'] ?? 'info',
            'icon' => $this->data['icon'] ?? null,
        ];
    }
}
```

### Types de notifications intégrés

Le système inclut plusieurs types de notifications prédéfinis :

- `AbsenceNotification` : Pour les notifications d'absences et de retards
- `PaymentNotification` : Pour les notifications de paiement
- `GradeNotification` : Pour les notes et évaluations
- `SystemNotification` : Pour les messages système
- `WelcomeNotification` : Pour l'accueil des nouveaux utilisateurs
- `PaymentReminderNotification` : Pour les rappels de paiement

### Envoi de notifications

Pour envoyer une notification à un utilisateur :

```php
use App\Notifications\NouvelleNotification;

// À un seul utilisateur
$user->notify(new NouvelleNotification([
    'titre' => 'Bienvenue !',
    'message' => 'Merci de vous être inscrit sur notre plateforme.',
    'url' => url('/tableau-de-bord'),
    'type' => 'success',
]));

// À plusieurs utilisateurs
Notification::send($users, new NouvelleNotification([...]));

// Via une file d'attente (recommandé pour les notifications multiples)
Notification::sendNow($users, new NouvelleNotification([...]));
```

## Utilisation côté frontend

### Composants disponibles

#### NotificationBell

Affiche une icône de cloche avec un compteur de notifications non lues.

```vue
<template>
  <NotificationBell />
</template>

<script>
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';

export default {
  components: {
    NotificationBell,
  },
};
</script>
```

#### ToastNotifications

Affiche des notifications toast temporaires.

```vue
<template>
  <ToastNotifications position="top-right" />
</template>

<script>
import ToastNotifications from '@/Components/Notifications/ToastNotifications.vue';

export default {
  components: {
    ToastNotifications,
  },
};
</script>
```

### Service de notifications

Le service `NotificationService` fournit des méthodes pour interagir avec l'API de notifications :

```javascript
import NotificationService from '@/Services/NotificationService';

// Récupérer les notifications
const response = await NotificationService.getNotifications({
  page: 1,
  per_page: 10,
  unread: true,
});

// Marquer une notification comme lue
await NotificationService.markAsRead(notificationId);

// Marquer toutes les notifications comme lues
await NotificationService.markAllAsRead();

// Supprimer une notification
await NotificationService.deleteNotification(notificationId);

// Récupérer les préférences
const preferences = await NotificationService.getPreferences();

// Mettre à jour les préférences
await NotificationService.updatePreferences({
  email: true,
  push: true,
  // ...
});
```

### Notifications en temps réel

Le service `RealtimeNotificationService` gère les notifications en temps réel :

```javascript
import RealtimeNotificationService from '@/Services/RealtimeNotificationService';

// Initialiser le service (à faire lors de la connexion de l'utilisateur)
RealtimeNotificationService.initialize(user, token);

// Écouter les nouvelles notifications
RealtimeNotificationService.on('new-notification', (notification) => {
  console.log('Nouvelle notification reçue :', notification);
  
  // Afficher une notification toast
  this.$notify({
    title: notification.data.title,
    message: notification.data.message,
    type: notification.data.type || 'info',
    onClick: () => {
      // Rediriger vers l'URL de la notification
      if (notification.data.url) {
        this.$inertia.visit(notification.data.url);
      }
    },
  });
});

// Mettre à jour le compteur de notifications non lues
RealtimeNotificationService.on('unread-count-updated', (count) => {
  console.log(`Nombre de notifications non lues : ${count}`);
});
```

## Personnalisation

### Créer un nouveau type de notification

1. Créez une nouvelle classe de notification :

```bash
php artisan make:notification NouveauTypeNotification
```

2. Implémentez les méthodes nécessaires :

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NouveauTypeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'url' => $this->data['url'] ?? null,
            'type' => $this->data['type'] ?? 'info',
            'icon' => $this->data['icon'] ?? null,
        ];
    }
}
```

### Ajouter un canal de notification

1. Créez une nouvelle classe de canal dans `app/Notifications/Channels` :

```php
<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class NouveauCanalChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toNouveauCanal($notifiable);
        
        // Implémentez la logique d'envoi ici
        // Par exemple, envoyer un SMS, une notification push, etc.
        
        return true;
    }
}
```

2. Enregistrez le canal dans `AppServiceProvider` :

```php
use Illuminate\Support\Facades\Notification;
use App\Notifications\Channels\NouveauCanalChannel;

public function boot()
{
    Notification::extend('nouveaucanal', function ($app) {
        return new NouveauCanalChannel();
    });
}
```

3. Utilisez le canal dans vos notifications :

```php
public function via($notifiable)
{
    return ['mail', 'database', 'nouveaucanal'];
}

public function toNouveauCanal($notifiable)
{
    return [
        'to' => $notifiable->phone_number,
        'message' => 'Nouvelle notification: ' . $this->data['message'],
    ];
}
```

## Tests

### Tests unitaires

Les tests unitaires pour les notifications se trouvent dans `tests/Unit/Notifications`.

Exemple de test :

```php
<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\NouvelleNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NouvelleNotificationTest extends TestCase
{
    public function test_notification_is_sent_to_user()
    {
        $user = User::factory()->create();
        
        Notification::fake();
        
        $user->notify(new NouvelleNotification([
            'titre' => 'Test',
            'message' => 'Ceci est un test',
        ]));
        
        Notification::assertSentTo(
            $user,
            NouvelleNotification::class,
            function ($notification, $channels) {
                return in_array('mail', $channels) && 
                       in_array('database', $channels);
            }
        );
    }
}
```

### Tests d'intégration

Les tests d'intégration pour l'API de notifications se trouvent dans `tests/Feature/NotificationTest.php`.

## Dépannage

### Les notifications ne s'affichent pas

1. Vérifiez que le système de notifications est activé dans la configuration.
2. Vérifiez que les événements de diffusion sont correctement configurés.
3. Vérifiez les logs d'erreur pour toute exception.

### Les notifications en temps réel ne fonctionnent pas

1. Vérifiez que le service de diffusion (Pusher) est correctement configuré.
2. Vérifiez que le client Echo est correctement initialisé.
3. Vérifiez que les canaux de diffusion sont correctement configurés.

### Les emails ne sont pas envoyés

1. Vérifiez la configuration du service d'email dans `.env`.
2. Vérifiez que la file d'attente est en cours d'exécution.
3. Vérifiez les logs de la file d'attente pour les erreurs.

## Conclusion

Ce système de notifications complet offre une solution flexible et extensible pour gérer les communications avec les utilisateurs. Il prend en charge plusieurs canaux de notification et peut être facilement étendu pour répondre aux besoins futurs de l'application.
