<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illine\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Supprimer la migration problématique
try {
    $deleted = DB::table('migrations')
        ->where('migration', '2025_08_29_220005_add_missing_foreign_keys')
        ->delete();
    
    if ($deleted) {
        echo "Migration problématique supprimée avec succès.\n";
    } else {
        echo "La migration problématique n'a pas été trouvée.\n";
    }
} catch (\Exception $e) {
    echo "Erreur lors de la suppression de la migration: " . $e->getMessage() . "\n";
}

echo "Veuvez exécuter 'php artisan migrate' pour continuer.\n";
