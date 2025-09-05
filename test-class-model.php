<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware_Console_Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

use App\Models\Classe;

try {
    // Get the latest class
    $lastClass = Classe::latest()->first();
    
    if ($lastClass) {
        echo "Latest Class:\n";
        echo "ID: " . $lastClass->id . "\n";
        echo "Nom: " . $lastClass->nom . "\n";
        echo "Code: " . $lastClass->code . "\n";
        echo "Niveau ID: " . $lastClass->niveau_id . "\n";
        echo "Filiere ID: " . $lastClass->filiere_id . "\n";
        echo "Effectif Max: " . $lastClass->effectif_max . "\n";
        echo "Est Actif: " . ($lastClass->est_actif ? 'Oui' : 'Non') . "\n";
        
        // Test relationships
        if ($lastClass->niveau) {
            echo "Niveau: " . $lastClass->niveau->nom . "\n";
        }
        
        if ($lastClass->filiere) {
            echo "Filiere: " . $lastClass->filiere->nom . "\n";
        }
        
        echo "Nom Complet: " . $lastClass->nom_complet . "\n";
        echo "Effectif Actuel: " . $lastClass->effectif_actuel . "\n";
        echo "Est Complet: " . ($lastClass->est_complete ? 'Oui' : 'Non') . "\n";
    } else {
        echo "Aucune classe trouvée dans la base de données.\n";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
