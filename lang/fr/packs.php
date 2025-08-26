<?php

return [
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
