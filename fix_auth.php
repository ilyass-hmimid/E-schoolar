<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Diagnostic et correction de l'authentification ===\n\n";

// 1. Nettoyer les logs
echo "1. Nettoyage des logs...\n";
if (file_exists('storage/logs/laravel.log')) {
    file_put_contents('storage/logs/laravel.log', '');
    echo "✓ Logs nettoyés\n";
}

// 2. Vérifier et créer les utilisateurs de test
echo "\n2. Vérification des utilisateurs de test...\n";

$testUsers = [
    [
        'email' => 'admin@test.com',
        'name' => 'Admin Test',
        'role' => \App\Enums\RoleType::ADMIN,
        'password' => 'password'
    ],
    [
        'email' => 'professeur@test.com',
        'name' => 'Professeur Test',
        'role' => \App\Enums\RoleType::PROFESSEUR,
        'password' => 'password'
    ],
    [
        'email' => 'assistant@test.com',
        'name' => 'Assistant Test',
        'role' => \App\Enums\RoleType::ASSISTANT,
        'password' => 'password'
    ],
    [
        'email' => 'eleve@test.com',
        'name' => 'Élève Test',
        'role' => \App\Enums\RoleType::ELEVE,
        'password' => 'password'
    ]
];

foreach ($testUsers as $userData) {
    $user = \App\Models\User::firstOrCreate(
        ['email' => $userData['email']],
        [
            'name' => $userData['name'],
            'password' => bcrypt($userData['password']),
            'role' => $userData['role'],
            'is_active' => true,
        ]
    );
    
    if ($user->wasRecentlyCreated) {
        echo "✓ Utilisateur {$userData['name']} créé\n";
    } else {
        echo "✓ Utilisateur {$userData['name']} existe déjà\n";
    }
}

// 3. Tester l'authentification
echo "\n3. Test d'authentification...\n";

foreach ($testUsers as $userData) {
    if (\Illuminate\Support\Facades\Auth::attempt([
        'email' => $userData['email'],
        'password' => $userData['password']
    ])) {
        $user = \Illuminate\Support\Facades\Auth::user();
        echo "✓ Authentification réussie pour {$user->name} (Rôle: {$user->role->label()})\n";
        \Illuminate\Support\Facades\Auth::logout();
    } else {
        echo "✗ Échec d'authentification pour {$userData['name']}\n";
    }
}

// 4. Vérifier les middlewares
echo "\n4. Test des middlewares...\n";

// Test avec un utilisateur admin connecté
if (\Illuminate\Support\Facades\Auth::attempt(['email' => 'admin@test.com', 'password' => 'password'])) {
    $user = \Illuminate\Support\Facades\Auth::user();
    
    // Test du middleware active
    try {
        $middleware = new \App\Http\Middleware\EnsureUserIsActive();
        $request = \Illuminate\Http\Request::create('/test');
        $request->setLaravelSession(\Illuminate\Support\Facades\Session::driver());
        
        $response = $middleware->handle($request, function($request) {
            return response('OK');
        });
        echo "✓ Middleware 'active' fonctionne\n";
    } catch (Exception $e) {
        echo "✗ Erreur middleware 'active': " . $e->getMessage() . "\n";
    }
    
    // Test du middleware role
    try {
        $roleMiddleware = new \App\Http\Middleware\CheckRole();
        $request = \Illuminate\Http\Request::create('/test');
        $request->setLaravelSession(\Illuminate\Support\Facades\Session::driver());
        
        $response = $roleMiddleware->handle($request, function($request) {
            return response('OK');
        }, 'admin');
        echo "✓ Middleware 'role' fonctionne\n";
    } catch (Exception $e) {
        echo "✗ Erreur middleware 'role': " . $e->getMessage() . "\n";
    }
    
    \Illuminate\Support\Facades\Auth::logout();
}

// 5. Vérifier les routes
echo "\n5. Vérification des routes...\n";

$router = app('router');
$routes = $router->getRoutes();

$dashboardRoute = null;
$loginRoute = null;

foreach ($routes as $route) {
    if ($route->getName() === 'dashboard') {
        $dashboardRoute = $route;
    }
    if ($route->getName() === 'login') {
        $loginRoute = $route;
    }
}

if ($dashboardRoute) {
    echo "✓ Route 'dashboard' trouvée\n";
    echo "  - URI: " . $dashboardRoute->uri() . "\n";
    echo "  - Middlewares: " . implode(', ', $dashboardRoute->middleware()) . "\n";
} else {
    echo "✗ Route 'dashboard' manquante\n";
}

if ($loginRoute) {
    echo "✓ Route 'login' trouvée\n";
} else {
    echo "✗ Route 'login' manquante\n";
}

// 6. Nettoyer les caches
echo "\n6. Nettoyage des caches...\n";

try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✓ Caches nettoyés\n";
} catch (Exception $e) {
    echo "✗ Erreur lors du nettoyage des caches: " . $e->getMessage() . "\n";
}

echo "\n=== Diagnostic terminé ===\n";
echo "\nPour tester l'authentification:\n";
echo "1. Allez sur http://localhost/login\n";
echo "2. Connectez-vous avec:\n";
echo "   - Admin: admin@test.com / password\n";
echo "   - Professeur: professeur@test.com / password\n";
echo "   - Assistant: assistant@test.com / password\n";
echo "   - Élève: eleve@test.com / password\n";
echo "\nSi le problème persiste, vérifiez:\n";
echo "- La configuration de la base de données dans .env\n";
echo "- Les permissions des dossiers storage et bootstrap/cache\n";
echo "- Les logs dans storage/logs/laravel.log\n";
