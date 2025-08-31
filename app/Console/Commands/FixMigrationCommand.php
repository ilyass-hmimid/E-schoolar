<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixMigrationCommand extends Command
{
    protected $signature = 'migrate:fix';
    protected $description = 'Supprime la migration problématique et corrige les contraintes en double';

    public function handle()
    {
        // Supprimer la migration problématique
        $deleted = DB::table('migrations')
            ->where('migration', '2025_08_29_220005_add_missing_foreign_keys')
            ->delete();
        
        if ($deleted) {
            $this->info('Migration problématique supprimée avec succès.');
        } else {
            $this->warn('La migration problématique n\'a pas été trouvée.');
        }
        
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Supprimer les contraintes problématiques
        $this->dropForeignIfExists('absences', 'absences_professeur_id_foreign');
        $this->dropForeignIfExists('absences', 'IDX_F9C0EFFFBAB22EE9');
        
        // Recréer les contraintes correctement
        if (Schema::hasTable('absences') && Schema::hasColumn('absences', 'professeur_id')) {
            Schema::table('absences', function ($table) {
                $table->foreign('professeur_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
            $this->info('Contrainte absences_professeur_id_foreign recréée avec succès.');
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->info('Correction terminée avec succès !');
        $this->line('Veuvez exécuter "php artisan migrate" pour continuer.');
    }
    
    protected function dropForeignIfExists($table, $constraint)
    {
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $tableDetails = $sm->listTableDetails($table);
            
            if ($tableDetails->hasForeignKey($constraint)) {
                Schema::table($table, function ($table) use ($constraint) {
                    $table->dropForeign($constraint);
                });
                $this->info("Contrainte $constraint supprimée avec succès.");
                return true;
            }
        } catch (\Exception $e) {
            $this->error("Erreur lors de la suppression de la contrainte $constraint: " . $e->getMessage());
        }
        
        return false;
    }
}
