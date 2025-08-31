<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Désactivation des contraintes de clé étrangère...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    echo "Suppression des contraintes problématiques...\n";
    
    // Supprimer les contraintes problématiques
    $constraints = [
        'absences_professeur_id_foreign',
        'IDX_F9C0EFFFBAB22EE9'
    ];
    
    foreach ($constraints as $constraintName) {
        try {
            echo "Tentative de suppression de la contrainte: $constraintName\n";
            DB::statement("ALTER TABLE absences DROP FOREIGN KEY IF EXISTS `$constraintName`");
            echo "Contrainte $constraintName supprimée avec succès.\n";
        } catch (Exception $e) {
            echo "Impossible de supprimer la contrainte $constraintName: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Recréation de la contrainte...\n";
    
    // Vérifier si la colonne existe avant de recréer la contrainte
    if (Schema::hasColumn('absences', 'professeur_id')) {
        echo "Recréation de la contrainte absences_professeur_id_foreign...\n";
        try {
            DB::statement("
                ALTER TABLE absences 
                ADD CONSTRAINT `absences_professeur_id_foreign` 
                FOREIGN KEY (`professeur_id`) 
                REFERENCES `users` (`id`) 
                ON DELETE SET NULL
            
            ");
            echo "Contrainte recréée avec succès.\n";
        } catch (Exception $e) {
            echo "Erreur lors de la recréation de la contrainte: " . $e->getMessage() . "\n";
        }
    } else {
        echo "La colonne professeur_id n'existe pas dans la table absences.\n";
    }
    
    echo "Réactivation des contraintes de clé étrangère...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    // Supprimer l'entrée de la migration problématique
    DB::table('migrations')
        ->where('migration', '2025_08_29_220005_add_missing_foreign_keys')
        ->delete();
    
    echo "Correction terminée avec succès !\n";
    echo "Vous pouvez maintenant exécuter 'php artisan migrate' pour continuer.\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    exit(1);
}
