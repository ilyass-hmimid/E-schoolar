<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;

// Supprimer les rôles numériques
echo "Nettoyage des rôles numériques...\n";
$numericRoles = Role::whereIn('name', ['1', '2', '3', '4'])->get();

echo count($numericRoles) . " rôles numériques trouvés.\n";

foreach ($numericRoles as $role) {
    echo "Suppression du rôle: " . $role->name . "\n";
    $role->delete();
}

echo "Nettoyage terminé. Rôles restants :\n";
foreach (Role::all() as $role) {
    echo "- {$role->name} (guard: {$role->guard_name})\n";
}
