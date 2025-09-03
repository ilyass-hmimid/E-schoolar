<?php

return [
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
        'actions' => 'Actions'
    ],
    'types' => [
        'absence' => 'Absence',
        'retard' => 'Retard',
        'sortie' => 'Sortie anticipée'
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
        'view_statistics' => 'Voir les statistiques'
    ],
    'confirm' => [
        'delete' => 'Êtes-vous sûr de vouloir supprimer cette absence ?',
        'justifier' => 'Êtes-vous sûr de vouloir marquer cette absence comme justifiée ?'
    ],
    'status' => [
        'justified' => 'Justifiée',
        'unjustified' => 'Non justifiée'
    ],
    'success' => [
        'created' => 'L\'absence a été enregistrée avec succès.',
        'updated' => 'L\'absence a été mise à jour avec succès.',
        'deleted' => 'L\'absence a été supprimée avec succès.',
        'justified' => 'L\'absence a été marquée comme justifiée.',
        'unjustified' => 'L\'absence a été marquée comme non justifiée.',
        'justificatif_uploaded' => 'Le justificatif a été téléversé avec succès.',
        'justificatif_removed' => 'Le justificatif a été supprimé avec succès.'
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
        'invalid_date' => 'La date d\'absence ne correspond pas au jour du cours sélectionné.'
    ],
    'filters' => [
        'all' => 'Toutes les absences',
        'justified' => 'Absences justifiées',
        'unjustified' => 'Absences non justifiées',
        'date_range' => 'Période',
        'student' => 'Élève',
        'class' => 'Classe',
        'apply' => 'Appliquer les filtres',
        'reset' => 'Réinitialiser'
    ],
    'stats' => [
        'total' => 'Total des absences',
        'justified' => 'Justifiées',
        'unjustified' => 'Non justifiées',
        'rate' => 'Taux d\'absence',
        'by_type' => 'Répartition par type',
        'by_month' => 'Évolution mensuelle'
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
];
