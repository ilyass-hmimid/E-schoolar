<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware_Console_Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Récupérer les libellés des filières
$filieres = DB::table('filieres')->select('libelle')->get();

echo "Filières dans la base de données :\n";
foreach ($filieres as $filiere) {
    echo "- " . $filiere->libelle . "\n";
}

// Vérifier les noms attendus par shouldCreateClass
$expectedFilieres = [
    'Sciences Mathématiques',
    'Lettres',
    'Sciences Humaines',
    'Sciences Physiques',
    'Sciences de la Vie et de la Terre',
    'Sciences Économiques'
];

echo "\nFilières attendues par shouldCreateClass :\n";
foreach ($expectedFilieres as $expected) {
    echo "- $expected\n";
}

// Vérifier si les filières attendues existent
$missing = [];
foreach ($expectedFilieres as $expected) {
    $exists = false;
    foreach ($filieres as $filiere) {
        if (trim($filiere->libelle) === trim($expected)) {
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        $missing[] = $expected;
    }
}

if (!empty($missing)) {
    echo "\nFilières manquantes :\n";
    foreach ($missing as $m) {
        echo "- $m\n";
    }
} else {
    echo "\nToutes les filières attendues sont présentes.\n";
}
