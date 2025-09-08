<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Niveaux et Classes
    |--------------------------------------------------------------------------
    |
    | Liste des niveaux et classes disponibles dans l'établissement.
    | Les classes sont organisées par niveau (primaire, collège, lycée).
    |
    */
    'niveaux' => [
        'primaire' => [
            '3ème année primaire',
            '4ème année primaire',
            '5ème année primaire',
            '6ème année primaire',
        ],
        'college' => [
            '1ère année collège',
            '2ème année collège',
            '3ème année collège',
        ],
        'lycee' => [
            'Tronc commun',
            '1ère année bac',
            '2ème année bac',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filières du Bac
    |--------------------------------------------------------------------------
    |
    | Liste des filières disponibles pour le baccalauréat.
    |
    */
    'filieres_bac' => [
        'Physique',
        'Sciences Maths',
        'SVT',
    ],

    /*
    |--------------------------------------------------------------------------
    | Langues d'enseignement
    |--------------------------------------------------------------------------
    |
    | Langues d'enseignement disponibles pour les classes du bac.
    |
    */
    'langues_enseignement' => [
        'Arabe',
        'Français',
    ],

    /*
    |--------------------------------------------------------------------------
    | Matières
    |--------------------------------------------------------------------------
    |
    | Liste des matières enseignées dans l'établissement.
    |
    */
    'matieres' => [
        'Mathématiques',
        'SVT',
        'Physique',
        'Communication Français',
        'Communication Anglais',
    ],

    /*
    |--------------------------------------------------------------------------
    | Matières par niveau
    |--------------------------------------------------------------------------
    |
    | Définit quelles matières sont enseignées à quel niveau.
    |
    */
    'matieres_par_niveau' => [
        'primaire' => [
            'Mathématiques',
            'Communication Français',
            'Communication Anglais',
        ],
        'college' => [
            'Mathématiques',
            'SVT',
            'Physique',
            'Communication Français',
            'Communication Anglais',
        ],
        'lycee' => [
            'Mathématiques',
            'SVT',
            'Physique',
            'Communication Français',
            'Communication Anglais',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Classes Spéciales
    |--------------------------------------------------------------------------
    |
    | Classes spéciales qui ne suivent pas la nomenclature standard.
    |
    */
    'classes_speciales' => [
        'Tronc commun',
    ],
];
