<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Désactiver la journalisation des requêtes SQL
DB::connection()->disableQueryLog();

echo "Nettoyage des permissions en double...\n";

// Liste des permissions à conserver (format resource.action)
$keepPermissions = [
    'users.view', 'users.create', 'users.edit', 'users.delete', 'users.impersonate',
    'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'roles.assign',
    'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',
    'eleves.view', 'eleves.create', 'eleves.edit', 'eleves.delete', 'eleves.export',
    'professeurs.view', 'professeurs.create', 'professeurs.edit', 'professeurs.delete',
    'classes.view', 'classes.create', 'classes.edit', 'classes.delete',
    'matieres.view', 'matieres.create', 'matieres.edit', 'matieres.delete',
    'emplois_du_temps.view', 'emplois_du_temps.create', 'emplois_du_temps.edit', 'emplois_du_temps.delete',
    'notes.view', 'notes.create', 'notes.edit', 'notes.delete', 'notes.export',
    'absences.view', 'absences.create', 'absences.edit', 'absences.delete', 'absences.justify', 'absences.export',
    'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete', 'paiements.export',
    'settings.view', 'settings.edit',
    'dashboard.view', 'dashboard.admin', 'dashboard.teacher', 'dashboard.student', 'dashboard.parent'
];

// 1. Créer les permissions manquantes
foreach ($keepPermissions as $permissionName) {
    Permission::firstOrCreate(
        ['name' => $permissionName],
        ['guard_name' => 'web']
    );
}

// 2. Supprimer les permissions qui ne sont pas dans la liste keepPermissions
$permissionsToDelete = Permission::whereNotIn('name', $keepPermissions)->get();
$deletedCount = $permissionsToDelete->count();

if ($deletedCount > 0) {
    echo "Suppression de $deletedCount permissions obsolètes...\n";
    foreach ($permissionsToDelete as $permission) {
        $permission->delete();
    }
}

// 3. Mettre à jour les rôles avec les bonnes permissions
$roles = [
    'admin' => [
        'users.*', 'roles.*', 'permissions.*',
        'eleves.*', 'professeurs.*', 'classes.*', 'matieres.*',
        'emplois_du_temps.*', 'notes.*', 'absences.*', 'paiements.*',
        'settings.*', 'dashboard.*'
    ],
    'professeur' => [
        'dashboard.view', 'dashboard.teacher',
        'eleves.view', 'classes.view', 'matieres.view',
        'emplois_du_temps.view', 'notes.view', 'notes.create', 'notes.edit',
        'absences.view', 'absences.create', 'absences.edit'
    ],
    'assistant' => [
        'dashboard.view',
        'eleves.view', 'classes.view', 'matieres.view',
        'emplois_du_temps.view', 'notes.view',
        'absences.view', 'absences.create', 'absences.edit', 'absences.justify',
        'paiements.view', 'paiements.create'
    ],
    'eleve' => [
        'dashboard.view', 'dashboard.student',
        'classes.view', 'emplois_du_temps.view',
        'notes.view', 'absences.view'
    ]
];

foreach ($roles as $roleName => $permissions) {
    $role = Role::where('name', $roleName)->first();
    
    if ($role) {
        echo "Mise à jour des permissions pour le rôle: $roleName\n";
        
        // Convertir les wildcards (ex: users.*) en permissions réelles
        $actualPermissions = [];
        foreach ($permissions as $permission) {
            if (str_ends_with($permission, '.*')) {
                $prefix = str_replace('.*', '', $permission);
                $matching = array_filter($keepPermissions, fn($p) => str_starts_with($p, "$prefix."));
                $actualPermissions = array_merge($actualPermissions, $matching);
            } else {
                $actualPermissions[] = $permission;
            }
        }
        
        $role->syncPermissions($actualPermissions);
    }
}

echo "\nNettoyage terminé. Vérification finale...\n\n";

// Vérification finale
$roles = Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "Rôle: {$role->name}\n";
    echo "Permissions: " . $role->permissions->count() . "\n\n";
}

echo "\nToutes les permissions (" . Permission::count() . "):\n";
foreach (Permission::orderBy('name')->get() as $permission) {
    echo "- {$permission->name}\n";
}
