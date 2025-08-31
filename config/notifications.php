<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Canaux de notification
    |--------------------------------------------------------------------------
    |
    | Cette option contrôle les canaux de notification disponibles dans l'application.
    | Vous pouvez activer ou désactiver les canaux selon vos besoins.
    |
    */

    'channels' => [
        'mail' => env('NOTIFICATION_MAIL_ENABLED', true),
        'sms' => env('NOTIFICATION_SMS_ENABLED', true),
        'database' => env('NOTIFICATION_DATABASE_ENABLED', true),
        'push' => env('NOTIFICATION_PUSH_ENABLED', false),
        'realtime' => env('NOTIFICATION_REALTIME_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des emails
    |--------------------------------------------------------------------------
    */
    'mail' => [
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'noreply@allotawjih.ma'),
            'name' => env('MAIL_FROM_NAME', 'Allo Tawjih'),
        ],
        'templates' => [
            'welcome' => 'emails.welcome',
            'password_reset' => 'emails.auth.password-reset',
            'absence_alert' => 'emails.absence.alert',
            'payment_confirmation' => 'emails.payment.confirmation',
            'grade_notification' => 'emails.grades.notification',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des SMS
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'twilio'), // twilio, nexmo, or custom
        'from' => env('SMS_FROM', 'AlloTawjih'),
        'templates' => [
            'absence_alert' => 'Cher tuteur, votre enfant {student_name} était absent le {date} à {time}.',
            'payment_reminder' => 'Rappel : Paiement de {amount} DHS dû pour {month} {year}.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des notifications push
    |--------------------------------------------------------------------------
    */
    'push' => [
        'enabled' => env('PUSH_NOTIFICATIONS_ENABLED', false),
        'providers' => [
            'firebase' => [
                'server_key' => env('FIREBASE_SERVER_KEY'),
                'sender_id' => env('FIREBASE_SENDER_ID'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de notification
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'absence' => [
            'notify_after_minutes' => 15, // Minutes après le début du cours pour notifier l'absence
            'notify_admin' => true,
            'threshold_for_warning' => 3, // Nombre d'absences avant avertissement
        ],
        'payment' => [
            'reminder_days_before' => [7, 3, 1], // Jours avant échéance pour rappel
            'late_fee_percentage' => 2, // Pourcentage de frais de retard par semaine
        ],
        'grades' => [
            'notify_on_new_grade' => true,
            'notify_on_final_grade' => true,
        ],
        'realtime' => [
            'enabled' => env('BROADCAST_DRIVER') !== null && env('BROADCAST_DRIVER') !== 'null',
            'driver' => env('BROADCAST_DRIVER', 'pusher'),
            'channel' => 'App.Models.User.{id}',
            'event' => 'NewNotificationEvent',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modèles de notification
    |--------------------------------------------------------------------------
    |
    | Définit les modèles de notification personnalisés pour les entités
    |
    */
    'models' => [
        'user' => \App\Models\User::class,
        'teacher' => \App\Models\Teacher::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des webhooks
    |--------------------------------------------------------------------------
    */
    'webhooks' => [
        'enabled' => env('NOTIFICATION_WEBHOOKS_ENABLED', false),
        'url' => env('NOTIFICATION_WEBHOOK_URL'),
        'secret' => env('NOTIFICATION_WEBHOOK_SECRET'),
        'events' => [
            'absence_created',
            'payment_received',
            'grade_updated',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites de taux
    |--------------------------------------------------------------------------
    |
    | Contrôle la fréquence d'envoi des notifications pour éviter le spam
    |
    */
    'rate_limits' => [
        'per_minute' => 60,
        'per_hour' => 1000,
        'per_day' => 10000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Journalisation
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('NOTIFICATION_LOGGING_ENABLED', true),
        'level' => env('NOTIFICATION_LOGGING_LEVEL', 'info'),
        'channel' => env('NOTIFICATION_LOGGING_CHANNEL', 'stack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des files d'attente
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'connection' => env('NOTIFICATION_QUEUE_CONNECTION', 'database'),
        'queue' => env('NOTIFICATION_QUEUE', 'notifications'),
        'delay' => env('NOTIFICATION_DELAY', 0),
        'tries' => env('NOTIFICATION_TRIES', 3),
        'timeout' => env('NOTIFICATION_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des modèles de notification
    |--------------------------------------------------------------------------
    */
    'templates' => [
        'paths' => [
            'emails' => resource_path('views/emails'),
            'sms' => resource_path('views/sms'),
        ],
        'cache' => [
            'enabled' => env('NOTIFICATION_TEMPLATE_CACHE_ENABLED', true),
            'duration' => env('NOTIFICATION_TEMPLATE_CACHE_DURATION', 3600), // en secondes
        ],
    ],
];
