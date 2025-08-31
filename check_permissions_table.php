<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    if (!Schema::hasTable('permissions')) {
        echo "La table 'permissions' n'existe pas.\n";
        exit(1);
    }
    
    $columns = Schema::getColumnListing('permissions');
    echo "Colonnes de la table 'permissions':\n";
    print_r($columns);
    
    echo "\nStructure complète de la table 'permissions':\n";
    $columnsInfo = DB::select('SHOW COLUMNS FROM permissions');
    print_r($columnsInfo);
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
