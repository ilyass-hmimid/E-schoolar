<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\User;

// Vérifier les rôles
echo "Rôles dans la base de données :\n";
foreach (Role::all() as $role) {
    echo "- {$role->name} (guard: {$role->guard_name})\n";
}

// Vérifier l'utilisateur admin
$admin = User::where('email', 'admin@allotawjih.com')->first();
if ($admin) {
    echo "\nUtilisateur admin trouvé. ID: {$admin->id}\n";
    echo "Rôles de l'utilisateur admin :\n";
    foreach ($admin->roles as $role) {
        echo "- {$role->name}\n";
    }
} else {
    echo "\nAucun utilisateur admin trouvé.\n";
}
