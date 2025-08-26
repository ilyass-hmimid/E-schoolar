<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Types de packs
    |--------------------------------------------------------------------------
    |
    | Cette option définit les différents types de packs disponibles dans l'application.
    | Chaque type peut avoir des propriétés spécifiques qui seront utilisées
    | dans toute l'application.
    |
    */
    'types' => [
        'cours' => [
            'name' => 'Cours',
            'description' => 'Pack de cours individuels',
            'icon' => 'academic-cap',
            'color' => 'indigo',
        ],
        'abonnement' => [
            'name' => 'Abonnement',
            'description' => 'Abonnement mensuel ou annuel',
            'icon' => 'calendar',
            'color' => 'green',
        ],
        'formation' => [
            'name' => 'Formation',
            'description' => 'Formation complète avec programme défini',
            'icon' => 'book-open',
            'color' => 'purple',
        ],
        'autre' => [
            'name' => 'Autre',
            'description' => 'Autre type de pack',
            'icon' => 'cube',
            'color' => 'gray',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Options par défaut
    |--------------------------------------------------------------------------
    |
    | Options par défaut pour les nouveaux packs
    |
    */
    'defaults' => [
        'type' => 'cours',
        'duree_jours' => 30,
        'est_actif' => true,
        'est_populaire' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Options d'affichage
    |--------------------------------------------------------------------------
    |
    | Options pour l'affichage des packs dans l'interface
    |
    */
    'display' => [
        'items_per_page' => 15,
        'recent_items' => 5,
        'popular_items' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Options de validation
    |--------------------------------------------------------------------------
    |
    | Règles de validation pour les packs
    |
    */
    'validation' => [
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:cours,abonnement,formation,autre',
        'prix' => 'required|numeric|min:0',
        'prix_promo' => 'nullable|numeric|min:0|lt:prix',
        'duree_jours' => 'required|integer|min:1',
        'est_actif' => 'boolean',
        'est_populaire' => 'boolean',
    ],

    /*
    |--------------------------------------------------------------------------
    | Options de recherche
    |--------------------------------------------------------------------------
    |
    | Options pour la recherche de packs
    |
    */
    'search' => [
        'min_length' => 2,
        'max_results' => 10,
        'highlight' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Options d'export
    |--------------------------------------------------------------------------
    |
    | Options pour l'export des données des packs
    |
    */
    'export' => [
        'formats' => ['csv', 'excel', 'pdf'],
        'columns' => [
            'id', 'nom', 'type', 'prix', 'prix_promo', 'duree_jours', 'est_actif', 'created_at'
        ],
    ],
];
