<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification de la configuration ===\n\n";

// 1. Vérifier le fichier .env
echo "1. Vérification du fichier .env...\n";
if (file_exists('.env')) {
    echo "✓ Fichier .env trouvé\n";
    
    $envContent = file_get_contents('.env');
    $requiredKeys = [
        'APP_NAME',
        'APP_ENV',
        'APP_KEY',
        'APP_DEBUG',
        'APP_URL',
        'DB_CONNECTION',
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD'
    ];
    
    foreach ($requiredKeys as $key) {
        if (strpos($envContent, $key . '=') !== false) {
            echo "  ✓ $key configuré\n";
        } else {
            echo "  ✗ $key manquant\n";
        }
    }
} else {
    echo "✗ Fichier .env manquant\n";
}

// 2. Vérifier la clé d'application
echo "\n2. Vérification de la clé d'application...\n";
$appKey = config('app.key');
if ($appKey && $appKey !== 'base64:') {
    echo "✓ Clé d'application configurée\n";
} else {
    echo "✗ Clé d'application manquante ou invalide\n";
    echo "  Exécutez: php artisan key:generate\n";
}

// 3. Vérifier la configuration de la base de données
echo "\n3. Vérification de la base de données...\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✓ Connexion à la base de données réussie\n";
    
    // Vérifier les tables
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    $tableNames = array_map(function($table) {
        return array_values((array)$table)[0];
    }, $tables);
    
    $requiredTables = ['users', 'migrations', 'password_reset_tokens', 'failed_jobs'];
    foreach ($requiredTables as $table) {
        if (in_array($table, $tableNames)) {
            echo "  ✓ Table $table existe\n";
        } else {
            echo "  ✗ Table $table manquante\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Erreur de connexion à la base de données: " . $e->getMessage() . "\n";
}

// 4. Vérifier les permissions des dossiers
echo "\n4. Vérification des permissions...\n";
$directories = [
    'storage' => '0775',
    'storage/logs' => '0775',
    'storage/framework' => '0775',
    'storage/framework/cache' => '0775',
    'storage/framework/sessions' => '0775',
    'storage/framework/views' => '0775',
    'bootstrap/cache' => '0775'
];

foreach ($directories as $dir => $permission) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✓ Dossier $dir accessible en écriture\n";
        } else {
            echo "✗ Dossier $dir non accessible en écriture\n";
        }
    } else {
        echo "✗ Dossier $dir n'existe pas\n";
    }
}

// 5. Vérifier les middlewares
echo "\n5. Vérification des middlewares...\n";
$middlewares = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'active' => \App\Http\Middleware\EnsureUserIsActive::class,
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\CheckPermission::class
];

foreach ($middlewares as $name => $class) {
    if (class_exists($class)) {
        echo "✓ Middleware $name ($class) existe\n";
    } else {
        echo "✗ Middleware $name ($class) manquant\n";
    }
}

// 6. Vérifier les modèles
echo "\n6. Vérification des modèles...\n";
$models = [
    'User' => \App\Models\User::class,
    'RoleType' => \App\Enums\RoleType::class
];

foreach ($models as $name => $class) {
    if (class_exists($class)) {
        echo "✓ Modèle $name ($class) existe\n";
    } else {
        echo "✗ Modèle $name ($class) manquant\n";
    }
}

// 7. Vérifier les contrôleurs
echo "\n7. Vérification des contrôleurs...\n";
$controllers = [
    'LoginController' => \App\Http\Controllers\Auth\LoginController::class,
    'DashboardController' => \App\Http\Controllers\DashboardController::class
];

foreach ($controllers as $name => $class) {
    if (class_exists($class)) {
        echo "✓ Contrôleur $name ($class) existe\n";
    } else {
        echo "✗ Contrôleur $name ($class) manquant\n";
    }
}

echo "\n=== Vérification terminée ===\n";
echo "\nRecommandations:\n";
echo "1. Si la clé d'application est manquante: php artisan key:generate\n";
echo "2. Si les tables sont manquantes: php artisan migrate\n";
echo "3. Si les permissions sont incorrectes: chmod -R 775 storage bootstrap/cache\n";
echo "4. Si les middlewares sont manquants: vérifiez les imports dans app/Http/Kernel.php\n";
