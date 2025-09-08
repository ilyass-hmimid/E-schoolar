<?php

return [
    // Suite du fichier existant
    // Titres des pages
    'titles' => [
        'index' => 'Gestion des Packs',
        'create' => 'Créer un Pack',
        'edit' => 'Modifier le Pack :name',
        'show' => 'Détails du Pack :name',
    ],

    // Libellés des champs
    'fields' => [
        'nom' => 'Nom du pack',
        'slug' => 'URL simplifiée',
        'description' => 'Description',
        'type' => 'Type de pack',
        'prix' => 'Prix (DH)',
        'prix_promo' => 'Prix promotionnel (DH)',
        'duree_jours' => 'Durée (en jours)',
        'est_actif' => 'Actif',
        'est_populaire' => 'Mettre en avant',
        'created_at' => 'Date de création',
        'updated_at' => 'Dernière mise à jour',
    ],

    // Types de packs
    'types' => [
        'cours' => 'Cours',
        'abonnement' => 'Abonnement',
        'formation' => 'Formation',
        'autre' => 'Autre',
    ],

    // Boutons
    // Gestion des absences
    'absences' => [
        'titles' => [
            'index' => 'Gestion des Absences',
            'create' => 'Enregistrer une Absence',
            'edit' => 'Modifier l\'Absence',
            'show' => 'Détails de l\'Absence',
            'statistics' => 'Statistiques des Absences'
        ],
        'fields' => [
            'eleve_id' => 'Élève',
            'cours_id' => 'Cours',
            'date_absence' => 'Date d\'absence',
            'type_absence' => 'Type d\'absence',
            'duree_absence' => 'Durée (minutes)',
            'justifiee' => 'Justifiée',
            'motif' => 'Motif',
            'commentaire' => 'Commentaire',
            'justificatif' => 'Justificatif',
            'justificatif_preview' => 'Aperçu du justificatif',
            'created_by' => 'Saisi par',
            'created_at' => 'Date de saisie',
            'updated_at' => 'Dernière mise à jour',
            'duration' => 'Durée',
            'minutes' => 'minutes',
            'reason' => 'Motif',
            'comments' => 'Commentaires',
            'course' => 'Cours',
            'student' => 'Élève',
            'date' => 'Date',
            'type' => 'Type',
            'status' => 'Statut',
            'actions' => 'Actions',
        ],
        'types' => [
            'absence' => 'Absence',
            'retard' => 'Retard',
            'sortie' => 'Sortie anticipée',
        ],
        'buttons' => [
            'create' => 'Enregistrer une absence',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler',
            'back' => 'Retour à la liste',
            'export' => 'Exporter en Excel',
            'justificatif' => 'Télécharger le justificatif',
            'generate_pdf' => 'Générer un PDF',
            'download_justificatif' => 'Télécharger le justificatif',
            'remove_justificatif' => 'Supprimer le justificatif',
            'choose_file' => 'Choisir un fichier',
            'no_file_chosen' => 'Aucun fichier choisi',
            'justify' => 'Justifier',
            'unjustify' => 'Déjustifier',
            'view_statistics' => 'Voir les statistiques',
        ],
        'confirm' => [
            'delete' => 'Êtes-vous sûr de vouloir supprimer cette absence ?',
            'justifier' => 'Êtes-vous sûr de vouloir marquer cette absence comme justifiée ?',
        ],
        'status' => [
            'justified' => 'Justifiée',
            'unjustified' => 'Non justifiée',
        ],
        'success' => [
            'created' => 'L\'absence a été enregistrée avec succès.',
            'updated' => 'L\'absence a été mise à jour avec succès.',
            'deleted' => 'L\'absence a été supprimée avec succès.',
            'justified' => 'L\'absence a été marquée comme justifiée.',
            'unjustified' => 'L\'absence a été marquée comme non justifiée.',
            'justificatif_uploaded' => 'Le justificatif a été téléversé avec succès.',
            'justificatif_removed' => 'Le justificatif a été supprimé avec succès.',
        ],
        'errors' => [
            'create' => 'Une erreur est survenue lors de l\'enregistrement de l\'absence.',
            'update' => 'Une erreur est survenue lors de la mise à jour de l\'absence.',
            'delete' => 'Une erreur est survenue lors de la suppression de l\'absence.',
            'not_found' => 'Absence non trouvée.',
            'justificatif_not_found' => 'Aucun justificatif trouvé pour cette absence.',
            'file_upload' => 'Une erreur est survenue lors du téléversement du fichier.',
            'invalid_file' => 'Le format du fichier n\'est pas valide.',
            'file_too_large' => 'Le fichier est trop volumineux. La taille maximale est de :size Mo.',
            'already_exists' => 'Une absence existe déjà pour cet élève à cette date et ce cours.',
            'invalid_date' => 'La date d\'absence ne correspond pas au jour du cours sélectionné.',
        ],
        'filters' => [
            'all' => 'Toutes les absences',
            'justified' => 'Absences justifiées',
            'unjustified' => 'Absences non justifiées',
            'date_range' => 'Période',
            'student' => 'Élève',
            'class' => 'Classe',
            'apply' => 'Appliquer les filtres',
            'reset' => 'Réinitialiser',
        ],
        'stats' => [
            'total' => 'Total des absences',
            'justified' => 'Justifiées',
            'unjustified' => 'Non justifiées',
            'rate' => 'Taux d\'absence',
            'by_type' => 'Répartition par type',
            'by_month' => 'Évolution mensuelle',
        ],
        'export' => [
            'title' => 'Export des absences',
            'description' => 'Exporter les absences selon les critères sélectionnés',
            'format' => 'Format d\'export',
            'options' => [
                'excel' => 'Excel (.xlsx)',
                'csv' => 'CSV (.csv)',
                'pdf' => 'PDF (.pdf)'
            ],
            'columns' => [
                'student' => 'Élève',
                'class' => 'Classe',
                'course' => 'Cours',
                'date' => 'Date',
                'type' => 'Type',
                'duration' => 'Durée (min)',
                'status' => 'Statut',
                'reason' => 'Motif',
                'comments' => 'Commentaires',
                'created_at' => 'Date de création',
                'created_by' => 'Créé par'
            ],
            'filters' => 'Filtres appliqués',
            'generate' => 'Générer l\'export',
            'downloading' => 'Téléchargement en cours...'
        ]
    ],

    'buttons' => [
        'create' => 'Créer un pack',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'back' => 'Retour à la liste',
        'show' => 'Voir les détails',
        'toggle_status' => 'Changer le statut',
        'toggle_popularity' => 'Mettre en avant',
        'duplicate' => 'Dupliquer',
    ],

    // Messages de confirmation
    'confirm' => [
        'delete' => 'Êtes-vous sûr de vouloir supprimer ce pack ?',
        'disable' => 'Êtes-vous sûr de vouloir désactiver ce pack ?',
        'enable' => 'Êtes-vous sûr de vouloir activer ce pack ?',
    ],

    // Messages de statut
    'status' => [
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'popular' => 'Mis en avant',
        'not_popular' => 'Non mis en avant',
    ],

    // Messages de succès
    'success' => [
        'created' => 'Le pack a été créé avec succès.',
        'updated' => 'Le pack a été mis à jour avec succès.',
        'deleted' => 'Le pack a été supprimé avec succès.',
        'status_updated' => 'Le statut du pack a été mis à jour.',
        'popularity_updated' => 'La mise en avant du pack a été mise à jour.',
        'duplicated' => 'Le pack a été dupliqué avec succès.',
    ],

    // Messages d'erreur
    'errors' => [
        'not_found' => 'Pack non trouvé.',
        'delete' => 'Une erreur est survenue lors de la suppression du pack.',
        'has_ventes' => 'Impossible de supprimer ce pack car il est associé à des ventes.',
        'update' => 'Une erreur est survenue lors de la mise à jour du pack.',
        'create' => 'Une erreur est survenue lors de la création du pack.',
    ],

    // Libellés des onglets
    'tabs' => [
        'details' => 'Détails',
        'ventes' => 'Ventes',
        'statistiques' => 'Statistiques',
    ],

    // Statistiques
    'stats' => [
        'total' => 'Total des packs',
        'active' => 'Packs actifs',
        'average_price' => 'Prix moyen',
        'total_sales' => 'Total des ventes',
        'recent_sales' => 'Ventes récentes',
        'no_recent_sales' => 'Aucune vente récente',
    ],

    // Filtres
    'filters' => [
        'all' => 'Tous les packs',
        'active' => 'Actifs',
        'inactive' => 'Inactifs',
        'popular' => 'Mis en avant',
        'type' => 'Type de pack',
        'search' => 'Rechercher un pack...',
    ],

    // Placeholders
    'placeholders' => [
        'search' => 'Rechercher par nom ou description...',
        'select_type' => 'Sélectionner un type',
        'select_status' => 'Sélectionner un statut',
    ],
];
