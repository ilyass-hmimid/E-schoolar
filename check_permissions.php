<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Vérifier les rôles et leurs permissions
echo "Vérification des rôles et permissions :\n\n";

$roles = Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "Rôle: {$role->name}\n";
    echo "Permissions:\n";
    
    if ($role->permissions->count() > 0) {
        foreach ($role->permissions as $permission) {
            echo "- {$permission->name}\n";
        }
    } else {
        echo "Aucune permission attribuée.\n";
    }
    
    echo "\n";
}

// Vérifier les permissions globales
echo "\nToutes les permissions disponibles :\n";
$permissions = Permission::all();

if ($permissions->count() > 0) {
    foreach ($permissions as $permission) {
        echo "- {$permission->name} ({$permission->guard_name})\n";
    }
} else {
    echo "Aucune permission trouvée dans la base de données.\n";
}
