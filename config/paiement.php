<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paramètres de paiement
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient les paramètres de configuration pour la gestion des paiements
    | et des notifications associées.
    |
    */

    // Nombre de jours avant qu'un paiement ne soit considéré en retard
    'jours_avant_retard' => 5,
    
    // Nombre de jours entre les rappels de retard
    'intervalle_rappel_retard' => 3,
    
    // Montant minimum pour notifier les administrateurs (en DH)
    'montant_notification_admin' => 5000,
    
    // Email de contact pour les questions de paiement
    'email_contact' => 'paiements@allotawjih.ma',
    
    // Activer les notifications par email
    'notifications_email' => env('PAIEMENT_NOTIFICATIONS_EMAIL', true),
    
    // Activer les notifications dans l'application
    'notifications_app' => env('PAIEMENT_NOTIFICATIONS_APP', true),
    
    // Activer les notifications SMS
    'notifications_sms' => env('PAIEMENT_NOTIFICATIONS_SMS', false),
    
    // Modèle de message pour les retards de paiement
    'messages' => [
        'retard_sujet' => 'Retard de paiement - Rappel',
        'retard_corps' => 'Bonjour :nom, nous vous rappelons que vous avez un retard de paiement de :montant DH concernant :matieres. Merci de régulariser votre situation au plus vite.',
        'confirmation_sujet' => 'Confirmation de paiement',
        'confirmation_corps' => 'Bonjour :nom, nous accusons réception de votre paiement de :montant DH. Merci pour votre confiance.',
    ],
];
