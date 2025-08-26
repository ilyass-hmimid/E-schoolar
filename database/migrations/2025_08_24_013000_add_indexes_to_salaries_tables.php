<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table salaires existe avant d'ajouter des index
        if (Schema::hasTable('salaires')) {
            Schema::table('salaires', function (Blueprint $table) {
                // Vérifier si l'index n'existe pas déjà
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('salaires');
                
                // Index pour les recherches par matière (s'il n'existe pas déjà)
                if (!array_key_exists('salaires_matiere_id_index', $indexes)) {
                    $table->index('matiere_id');
                }
            });
        }

        // Vérifier si la table configuration_salaires existe avant d'ajouter des index
        if (Schema::hasTable('configuration_salaires')) {
            Schema::table('configuration_salaires', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('configuration_salaires');
                
                // Index pour les recherches par matière
                if (!array_key_exists('configuration_salaires_matiere_id_index', $indexes)) {
                    $table->index('matiere_id');
                }
                
                // Index pour filtrer les configurations actives
                if (!array_key_exists('configuration_salaires_est_actif_index', $indexes)) {
                    $table->index('est_actif');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression des index de la table des salaires si elle existe
        if (Schema::hasTable('salaires')) {
            Schema::table('salaires', function (Blueprint $table) {
                $table->dropIndexIfExists('salaires_matiere_id_index');
                
                // Suppression des index supplémentaires si ils existent
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('salaires');
                
                if (array_key_exists('salaires_statut_mois_periode_index', $indexes)) {
                    $table->dropIndex('salaires_statut_mois_periode_index');
                }
            });
        }

        // Suppression des index de la table de configuration des salaires si elle existe
        if (Schema::hasTable('configuration_salaires')) {
            Schema::table('configuration_salaires', function (Blueprint $table) {
                $table->dropIndexIfExists('configuration_salaires_matiere_id_index');
                $table->dropIndexIfExists('configuration_salaires_est_actif_index');
            });
        }
    }
};
