<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Vérifier si l'utilisateur admin existe
$user = User::where('email', 'admin@teste.com')->first();

if (!$user) {
    die("L'utilisateur admin@teste.com n'existe pas.\n");
}

echo "=== Informations de l'utilisateur ===\n";
echo "ID: " . $user->id . "\n";
echo "Nom: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Actif: " . ($user->is_active ? 'Oui' : 'Non') . "\n\n";

// Vérifier les rôles
echo "=== Rôles ===\n";
$roles = $user->getRoleNames();
if ($roles->isEmpty()) {
    echo "Aucun rôle attribué.\n";
    
    // Essayer d'attribuer le rôle admin
    $adminRole = Role::where('name', 'admin')->first();
    if ($adminRole) {
        $user->assignRole($adminRole);
        echo "Le rôle 'admin' a été attribué à l'utilisateur.\n";
    } else {
        echo "Le rôle 'admin' n'existe pas. Création...\n";
        $adminRole = Role::create(['name' => 'admin']);
        $user->assignRole($adminRole);
        echo "Le rôle 'admin' a été créé et attribué.\n";
    }
} else {
    echo "Rôles: " . $roles->implode(', ') . "\n";
}

// Vérifier les permissions
echo "\n=== Permissions ===\n";
$permissions = $user->getAllPermissions();
if ($permissions->isEmpty()) {
    echo "Aucune permission directe.\n";
} else {
    echo "Permissions: " . $permissions->pluck('name')->implode(', ') . "\n";
}

// Vérifier les permissions via les rôles
echo "\n=== Permissions via les rôles ===\n";
$rolePermissions = collect();
foreach ($user->roles as $role) {
    $rolePermissions = $rolePermissions->merge($role->permissions->pluck('name'));
}
$rolePermissions = $rolePermissions->unique();

echo $rolePermissions->isEmpty() ? "Aucune permission via les rôles.\n" : $rolePermissions->implode(', ') . "\n";

// Vérifier si l'utilisateur a la permission d'accéder au tableau de bord
echo "\n=== Vérification de l'accès au tableau de bord ===\n";
if ($user->can('access dashboard')) {
    echo "L'utilisateur a la permission d'accéder au tableau de bord.\n";
} else {
    echo "ATTENTION: L'utilisateur n'a PAS la permission d'accéder au tableau de bord.\n";
}

echo "\n=== Solution recommandée ===\n";
if (!$user->hasRole('admin')) {
    echo "1. Attribution du rôle 'admin' à l'utilisateur... ";
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $user->assignRole($adminRole);
    echo "Fait!\n";
}

// Vérifier les permissions de base
$permissions = [
    'access dashboard',
    'view users',
    'create users',
    'edit users',
    'delete users'
];

echo "2. Vérification des permissions de base...\n";
$adminRole = Role::where('name', 'admin')->first();
if ($adminRole) {
    foreach ($permissions as $permission) {
        if (!$adminRole->hasPermissionTo($permission)) {
            echo "   - Ajout de la permission '$permission'... ";
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
            echo "Fait!\n";
        }
    }
}

echo "\n=== Vérification finale ===\n";
echo "L'utilisateur a maintenant le rôle 'admin' avec toutes les permissions nécessaires.\n";
echo "Essayez de vous reconnecter avec les identifiants suivants :\n";
echo "Email: admin@teste.com\n";
echo "Mot de passe: motdepass\n";
