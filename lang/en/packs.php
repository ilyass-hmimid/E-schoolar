<?php

return [
    // Page titles
    'titles' => [
        'index' => 'Packs Management',
        'create' => 'Create a Pack',
        'edit' => 'Edit Pack: :name',
        'show' => 'Pack Details: :name',
    ],

    // Field labels
    'fields' => [
        'nom' => 'Pack name',
        'slug' => 'Slug',
        'description' => 'Description',
        'type' => 'Pack type',
        'prix' => 'Price (MAD)',
        'prix_promo' => 'Promotional price (MAD)',
        'duree_jours' => 'Duration (days)',
        'est_actif' => 'Active',
        'est_populaire' => 'Featured',
        'created_at' => 'Creation date',
        'updated_at' => 'Last update',
    ],

    // Pack types
    'types' => [
        'cours' => 'Course',
        'abonnement' => 'Subscription',
        'formation' => 'Training',
        'autre' => 'Other',
    ],

    // Buttons
    'buttons' => [
        'create' => 'Create pack',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'back' => 'Back to list',
        'show' => 'View details',
        'toggle_status' => 'Toggle status',
        'toggle_popularity' => 'Feature',
        'duplicate' => 'Duplicate',
    ],

    // Confirmation messages
    'confirm' => [
        'delete' => [
            'title' => 'Delete Pack',
            'message' => 'Are you sure you want to delete this pack?',
            'button' => 'Yes, delete it',
        ],
        'deactivate' => [
            'title' => 'Deactivate Pack',
            'message' => 'Are you sure you want to deactivate this pack?',
            'button' => 'Yes, deactivate it',
        ],
        'duplicate' => [
            'title' => 'Duplicate Pack',
            'message' => 'Are you sure you want to duplicate this pack?',
            'button' => 'Yes, duplicate it',
        ],
    ],

    // Messages de succès
    'success' => [
        'created' => 'Pack created successfully!',
        'updated' => 'Pack updated successfully!',
        'deleted' => 'Pack deleted successfully!',
        'status_updated' => 'Pack status updated successfully!',
        'popularity_updated' => 'Pack featured status updated successfully!',
        'duplicated' => 'Pack duplicated successfully!',
    ],

    // Messages d'erreur
    'errors' => [
        'not_found' => 'Pack not found',
        'delete_restricted' => 'Cannot delete pack because it is associated with sales or registrations',
        'update_restricted' => 'Cannot update pack because it is associated with sales',
    ],

    // Table headers
    'table' => [
        'name' => 'Name',
        'type' => 'Type',
        'price' => 'Price',
        'duration' => 'Duration',
        'status' => 'Status',
        'actions' => 'Actions',
    ],

    // Status
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'featured' => 'Featured',
    ],

    // Statistics
    'statistics' => [
        'title' => 'Pack Statistics',
        'total' => 'Total Packs',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'featured' => 'Featured',
        'average_price' => 'Average Price',
        'total_sales' => 'Total Sales',
        'usage_rate' => 'Usage Rate',
    ],

    // Validation messages
    'validation' => [
        'name_required' => 'The pack name is required',
        'type_required' => 'The pack type is required',
        'price_required' => 'The price is required',
        'price_numeric' => 'The price must be a number',
        'price_min' => 'The price must be at least 0',
        'promo_price_lt' => 'The promotional price must be less than the regular price',
        'duration_required' => 'The duration is required',
        'duration_integer' => 'The duration must be a whole number',
        'duration_min' => 'The duration must be at least 1 day',
    ],
];
