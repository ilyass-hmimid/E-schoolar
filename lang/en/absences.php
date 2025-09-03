<?php

return [
    'titles' => [
        'index' => 'Absence Management',
        'create' => 'Record an Absence',
        'edit' => 'Edit Absence',
        'show' => 'Absence Details',
        'statistics' => 'Absence Statistics'
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
        'actions' => 'Actions'
    ],
    'types' => [
        'absence' => 'Absence',
        'retard' => 'Lateness',
        'sortie' => 'Early Departure'
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
        'view_statistics' => 'View Statistics'
    ],
    'confirm' => [
        'delete' => 'Are you sure you want to delete this absence?',
        'justifier' => 'Are you sure you want to mark this absence as justified?'
    ],
    'status' => [
        'justified' => 'Justified',
        'unjustified' => 'Unjustified'
    ],
    'success' => [
        'created' => 'Absence recorded successfully.',
        'updated' => 'Absence updated successfully.',
        'deleted' => 'Absence deleted successfully.',
        'justified' => 'Absence marked as justified.',
        'unjustified' => 'Absence marked as unjustified.',
        'justificatif_uploaded' => 'Proof document uploaded successfully.',
        'justificatif_removed' => 'Proof document removed successfully.'
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
        'invalid_date' => 'The absence date does not match the selected course day.'
    ],
    'filters' => [
        'all' => 'All Absences',
        'justified' => 'Justified Absences',
        'unjustified' => 'Unjustified Absences',
        'date_range' => 'Period',
        'student' => 'Student',
        'class' => 'Class',
        'apply' => 'Apply Filters',
        'reset' => 'Reset'
    ],
    'stats' => [
        'total' => 'Total Absences',
        'justified' => 'Justified',
        'unjustified' => 'Unjustified',
        'rate' => 'Absence Rate',
        'by_type' => 'Distribution by Type',
        'by_month' => 'Monthly Trend'
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
];
