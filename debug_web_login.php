<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== DIAGNOSTIC WEB LOGIN ===\n\n";

// 1. Vérifier l'utilisateur admin
echo "1. Vérification de l'utilisateur admin:\n";
$admin = User::where('email', 'admin@test.com')->first();

if ($admin) {
    echo "✅ Utilisateur trouvé: {$admin->name}\n";
    echo "   Email: {$admin->email}\n";
    echo "   Rôle: {$admin->role->value}\n";
    echo "   Actif: " . ($admin->is_active ? 'Oui' : 'Non') . "\n";
    
    // Vérifier le mot de passe
    if (Hash::check('password', $admin->password)) {
        echo "✅ Mot de passe correct\n";
    } else {
        echo "❌ Mot de passe incorrect\n";
    }
} else {
    echo "❌ Utilisateur admin non trouvé\n";
}

echo "\n2. Test d'authentification:\n";
try {
    $credentials = [
        'email' => 'admin@test.com',
        'password' => 'password'
    ];
    
    if (Auth::attempt($credentials)) {
        echo "✅ Authentification réussie\n";
        $user = Auth::user();
        echo "   Utilisateur connecté: {$user->name}\n";
        echo "   Rôle: {$user->role->value}\n";
        echo "   Actif: " . ($user->is_active ? 'Oui' : 'Non') . "\n";
        
        // Test de redirection
        echo "\n3. Test de redirection:\n";
        $redirect = match($user->role) {
            \App\Enums\RoleType::ADMIN => 'dashboard',
            \App\Enums\RoleType::PROFESSEUR => 'dashboard',
            \App\Enums\RoleType::ASSISTANT => 'dashboard',
            \App\Enums\RoleType::ELEVE => 'dashboard',
            \App\Enums\RoleType::PARENT => 'dashboard',
            default => 'dashboard',
        };
        echo "✅ Redirection vers: {$redirect}\n";
        
        Auth::logout();
    } else {
        echo "❌ Authentification échouée\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de l'authentification:\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . "\n";
    echo "   Ligne: " . $e->getLine() . "\n";
}

echo "\n4. Vérification des routes:\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $dashboardRoute = null;
    
    foreach ($routes as $route) {
        if ($route->getName() === 'dashboard') {
            $dashboardRoute = $route;
            break;
        }
    }
    
    if ($dashboardRoute) {
        echo "✅ Route dashboard trouvée: " . $dashboardRoute->getActionName() . "\n";
    } else {
        echo "❌ Route dashboard non trouvée\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de la vérification des routes: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
