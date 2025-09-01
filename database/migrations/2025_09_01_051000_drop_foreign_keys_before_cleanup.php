<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        if (config('database.default') !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        // Supprimer les contraintes de clé étrangère problématiques
        if (Schema::hasTable('etudiants')) {
            Schema::table('etudiants', function (Blueprint $table) {
                // Supprimer les contraintes de clé étrangère
                $foreignKeys = collect(DB::select("SHOW CREATE TABLE etudiants"))->first();
                
                if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_user_id_foreign`')) {
                    $table->dropForeign('etudiants_user_id_foreign');
                }
                
                if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_classe_id_foreign`')) {
                    $table->dropForeign('etudiants_classe_id_foreign');
                }
                
                if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_niveau_id_foreign`')) {
                    $table->dropForeign('etudiants_niveau_id_foreign');
                }
                
                if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_filiere_id_foreign`')) {
                    $table->dropForeign('etudiants_filiere_id_foreign');
                }
                
                // Supprimer les index
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('etudiants');
                
                if (array_key_exists('etudiants_user_classe_index', $indexes)) {
                    $table->dropIndex('etudiants_user_classe_index');
                }
            });
        }

        // Réactiver les contraintes de clé étrangère
        if (config('database.default') !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        // Cette migration supprime des contraintes, donc la méthode down() ne fait rien
        // car il n'est pas possible de recréer automatiquement les contraintes supprimées
    }
};
