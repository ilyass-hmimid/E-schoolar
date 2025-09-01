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
    
    // Absence Management
    'absences' => [
        'titles' => [
            'index' => 'Absence Management',
            'create' => 'Record an Absence',
            'edit' => 'Edit Absence',
            'show' => 'Absence Details',
            'statistics' => 'Absence Statistics',
        ],
        'fields' => [
            'eleve_id' => 'Student',
            'cours_id' => 'Course',
            'date_absence' => 'Absence Date',
            'type_absence' => 'Absence Type',
            'duree_absence' => 'Duration (minutes)',
            'justifiee' => 'Justified',
            'motif' => 'Reason',
            'commentaire' => 'Comments',
            'justificatif' => 'Proof Document',
            'justificatif_preview' => 'Document Preview',
            'created_by' => 'Recorded By',
            'created_at' => 'Recorded At',
            'updated_at' => 'Last Updated',
            'duration' => 'Duration',
            'minutes' => 'minutes',
            'reason' => 'Reason',
            'comments' => 'Comments',
            'course' => 'Course',
            'student' => 'Student',
            'date' => 'Date',
            'type' => 'Type',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
        'types' => [
            'absence' => 'Absence',
            'retard' => 'Lateness',
            'sortie' => 'Early Departure',
        ],
        'buttons' => [
            'create' => 'Record Absence',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'save' => 'Save',
            'cancel' => 'Cancel',
            'back' => 'Back to List',
            'export' => 'Export to Excel',
            'justificatif' => 'Download Proof',
            'generate_pdf' => 'Generate PDF',
            'download_justificatif' => 'Download Proof',
            'remove_justificatif' => 'Remove Proof',
            'choose_file' => 'Choose File',
            'no_file_chosen' => 'No file chosen',
            'justify' => 'Justify',
            'unjustify' => 'Unjustify',
            'notify_parents' => 'Notify Parents',
            'view_statistics' => 'View Statistics',
        ],
        'confirm' => [
            'delete' => 'Are you sure you want to delete this absence?',
            'justifier' => 'Are you sure you want to mark this absence as justified?',
        ],
        'status' => [
            'justified' => 'Justified',
            'unjustified' => 'Unjustified',
        ],
        'success' => [
            'created' => 'Absence recorded successfully.',
            'updated' => 'Absence updated successfully.',
            'deleted' => 'Absence deleted successfully.',
            'justified' => 'Absence marked as justified.',
            'unjustified' => 'Absence marked as unjustified.',
            'notified' => 'Parents have been notified successfully.',
            'justificatif_uploaded' => 'Proof document uploaded successfully.',
            'justificatif_removed' => 'Proof document removed successfully.',
        ],
        'errors' => [
            'create' => 'An error occurred while recording the absence.',
            'update' => 'An error occurred while updating the absence.',
            'delete' => 'An error occurred while deleting the absence.',
            'not_found' => 'Absence not found.',
            'justificatif_not_found' => 'No proof document found for this absence.',
            'file_upload' => 'An error occurred while uploading the file.',
            'invalid_file' => 'The file format is not valid.',
            'file_too_large' => 'The file is too large. Maximum size is :size MB.',
            'already_exists' => 'An absence already exists for this student on this date and course.',
            'invalid_date' => 'The absence date does not match the selected course day.',
        ],
        'filters' => [
            'all' => 'All Absences',
            'justified' => 'Justified Absences',
            'unjustified' => 'Unjustified Absences',
            'date_range' => 'Period',
            'student' => 'Student',
            'class' => 'Class',
            'apply' => 'Apply Filters',
            'reset' => 'Reset',
        ],
        'stats' => [
            'total' => 'Total Absences',
            'justified' => 'Justified',
            'unjustified' => 'Unjustified',
            'rate' => 'Absence Rate',
            'by_type' => 'Distribution by Type',
            'by_month' => 'Monthly Trend',
        ],
        'export' => [
            'title' => 'Export Absences',
            'description' => 'Export absences based on selected criteria',
            'format' => 'Export Format',
            'options' => [
                'excel' => 'Excel (.xlsx)',
                'csv' => 'CSV (.csv)',
                'pdf' => 'PDF (.pdf)'
            ],
            'columns' => [
                'student' => 'Student',
                'class' => 'Class',
                'course' => 'Course',
                'date' => 'Date',
                'type' => 'Type',
                'duration' => 'Duration (min)',
                'status' => 'Status',
                'reason' => 'Reason',
                'comments' => 'Comments',
                'created_at' => 'Created At',
                'created_by' => 'Created By'
            ],
            'filters' => 'Applied Filters',
            'generate' => 'Generate Export',
            'downloading' => 'Downloading...'
        ]
    ],
];
