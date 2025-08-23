<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Correction automatique des problèmes courants ===\n\n";

// 1. Générer la clé d'application si nécessaire
echo "1. Vérification de la clé d'application...\n";
$appKey = config('app.key');
if (!$appKey || $appKey === 'base64:') {
    echo "  Génération de la clé d'application...\n";
    try {
        \Illuminate\Support\Facades\Artisan::call('key:generate');
        echo "  ✓ Clé d'application générée\n";
    } catch (Exception $e) {
        echo "  ✗ Erreur lors de la génération de la clé: " . $e->getMessage() . "\n";
    }
} else {
    echo "  ✓ Clé d'application déjà configurée\n";
}

// 2. Exécuter les migrations si nécessaire
echo "\n2. Vérification des migrations...\n";
try {
    $pendingMigrations = \Illuminate\Support\Facades\Artisan::call('migrate:status');
    if (strpos($pendingMigrations, 'No pending migrations') === false) {
        echo "  Exécution des migrations...\n";
        \Illuminate\Support\Facades\Artisan::call('migrate');
        echo "  ✓ Migrations exécutées\n";
    } else {
        echo "  ✓ Toutes les migrations sont à jour\n";
    }
} catch (Exception $e) {
    echo "  ✗ Erreur lors des migrations: " . $e->getMessage() . "\n";
}

// 3. Nettoyer tous les caches
echo "\n3. Nettoyage des caches...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "  ✓ Caches nettoyés\n";
} catch (Exception $e) {
    echo "  ✗ Erreur lors du nettoyage des caches: " . $e->getMessage() . "\n";
}

// 4. Créer les utilisateurs de test
echo "\n4. Création des utilisateurs de test...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'TestUsersSeeder']);
    echo "  ✓ Utilisateurs de test créés\n";
} catch (Exception $e) {
    echo "  ✗ Erreur lors de la création des utilisateurs: " . $e->getMessage() . "\n";
    
    // Créer manuellement les utilisateurs
    echo "  Création manuelle des utilisateurs...\n";
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
            echo "    ✓ Utilisateur {$userData['name']} créé\n";
        } else {
            echo "    ✓ Utilisateur {$userData['name']} existe déjà\n";
        }
    }
}

// 5. Vérifier et corriger les permissions des dossiers
echo "\n5. Vérification des permissions...\n";
$directories = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (!is_writable($dir)) {
            echo "  Correction des permissions pour $dir...\n";
            try {
                chmod($dir, 0775);
                echo "    ✓ Permissions corrigées pour $dir\n";
            } catch (Exception $e) {
                echo "    ✗ Impossible de corriger les permissions pour $dir\n";
            }
        } else {
            echo "  ✓ Permissions correctes pour $dir\n";
        }
    } else {
        echo "  ✗ Dossier $dir n'existe pas\n";
    }
}

// 6. Vérifier la configuration de la base de données
echo "\n6. Test de la connexion à la base de données...\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "  ✓ Connexion à la base de données réussie\n";
} catch (Exception $e) {
    echo "  ✗ Erreur de connexion à la base de données: " . $e->getMessage() . "\n";
    echo "  Vérifiez votre fichier .env et la configuration de la base de données\n";
}

// 7. Test final d'authentification
echo "\n7. Test d'authentification...\n";
try {
    if (\Illuminate\Support\Facades\Auth::attempt([
        'email' => 'admin@test.com',
        'password' => 'password'
    ])) {
        $user = \Illuminate\Support\Facades\Auth::user();
        echo "  ✓ Authentification réussie pour {$user->name}\n";
        \Illuminate\Support\Facades\Auth::logout();
    } else {
        echo "  ✗ Échec de l'authentification\n";
    }
} catch (Exception $e) {
    echo "  ✗ Erreur lors du test d'authentification: " . $e->getMessage() . "\n";
}

echo "\n=== Correction terminée ===\n";
echo "\nVotre application devrait maintenant fonctionner correctement.\n";
echo "\nPour tester:\n";
echo "1. Démarrez votre serveur: php artisan serve\n";
echo "2. Allez sur http://localhost:8000/login\n";
echo "3. Connectez-vous avec:\n";
echo "   - Admin: admin@test.com / password\n";
echo "   - Professeur: professeur@test.com / password\n";
echo "   - Assistant: assistant@test.com / password\n";
echo "   - Élève: eleve@test.com / password\n";
echo "\nSi vous rencontrez encore des problèmes, vérifiez:\n";
echo "- Les logs dans storage/logs/laravel.log\n";
echo "- La configuration de votre serveur web\n";
echo "- Les permissions des dossiers storage et bootstrap/cache\n";
