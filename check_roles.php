<?php

echo "Démarrage du script de vérification des rôles...\n";

// Charger l'autoloader de Composer
require __DIR__.'/vendor/autoload.php';

// Démarrer l'application Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

// Créer une instance du noyau de console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Démarrer l'application
$kernel->bootstrap();

// Importer les classes nécessaires
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Fonction pour afficher une section
function showSection($title) {
    echo "\n\n=== $title ===\n";
}

try {
    // 1. Vérifier les rôles existants
    showSection("RÔLES EXISTANTS");
    $roles = Role::all();
    
    if ($roles->isEmpty()) {
        echo "Aucun rôle trouvé. Création des rôles par défaut...\n";
        $rolesToCreate = [
            ['name' => 'admin', 'description' => 'Administrateur système'],
            ['name' => 'professeur', 'description' => 'Enseignant'],
            ['name' => 'eleve', 'description' => 'Élève'],
            ['name' => 'assistant', 'description' => 'Assistant pédagogique']
        ];
        
        foreach ($rolesToCreate as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['guard_name' => 'web', 'description' => $roleData['description']]
            );
            echo "- Créé : {$roleData['name']}\n";
        }
        $roles = Role::all();
    }
    
    foreach ($roles as $role) {
        echo "- {$role->name} (Guard: {$role->guard_name})\n";
    }
    
    // 2. Vérifier les utilisateurs
    showSection("UTILISATEURS");
    $users = User::all();
    
    if ($users->isEmpty()) {
        echo "Aucun utilisateur trouvé dans la base de données.\n";
    } else {
        foreach ($users as $user) {
            echo "\nUtilisateur: {$user->email} (ID: {$user->id})\n";
            echo "Rôles: " . $user->getRoleNames()->implode(', ') . "\n";
            echo "Est admin: " . ($user->hasRole('admin') ? 'Oui' : 'Non') . "\n";
        }
    }
    
    // 3. Vérifier les permissions
    showSection("PERMISSIONS");
    $permissions = Permission::all();
    
    if ($permissions->isEmpty()) {
        echo "Aucune permission définie.\n";
    } else {
        foreach ($permissions as $permission) {
            echo "- {$permission->name} (Guard: {$permission->guard_name})\n";
        }
    }
    
    // 4. Vérifier et mettre à jour l'utilisateur admin
    showSection("VÉRIFICATION ADMIN");
    $adminEmail = 'admin@test.com';
    $admin = User::where('email', $adminEmail)->first();
    
    if ($admin) {
        echo "Utilisateur admin trouvé.\n";
        echo "ID: {$admin->id}\n";
        echo "Email: {$admin->email}\n";
        
        // Vérifier et attribuer le rôle admin
        if (!$admin->hasRole('admin')) {
            echo "ATTENTION: L'utilisateur n'a pas le rôle admin!\n";
            $admin->assignRole('admin');
            echo "-> Rôle admin attribué avec succès à {$admin->email}.\n";
        } else {
            echo "-> L'utilisateur a déjà le rôle admin.\n";
        }
        
        // Afficher les rôles actuels
        echo "Rôles actuels: " . $admin->getRoleNames()->implode(', ') . "\n";
        
    } else {
        echo "ATTENTION: Aucun utilisateur trouvé avec l'email {$adminEmail}.\n";
        // Créer un nouvel utilisateur admin si nécessaire
        // $password = Hash::make('votre_mot_de_passe_secure');
        // $admin = User::create([
        //     'name' => 'Admin',
        //     'email' => $adminEmail,
        //     'password' => $password,
        //     'is_active' => true
        // ]);
        // $admin->assignRole('admin');
        // echo "-> Nouvel utilisateur admin créé avec succès.\n";
    }
    
    showSection("VÉRIFICATION TERMINÉE");
    
} catch (Exception $e) {
    echo "\n\nERREUR: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . " (Ligne: " . $e->getLine() . ")\n";
}

echo "\n=== FIN DU SCRIPT ===\n";
