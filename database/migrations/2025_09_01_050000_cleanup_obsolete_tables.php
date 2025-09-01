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

        // Supprimer les tables obsolètes si elles existent
        $tablesToDrop = [
            'etudiants',
            'professeurs',
            'enseignants',
            'etudiant_matiere',
            'matiere_professeur',
            'salaires_old',
            'paiements_old',
            'historique_inscriptions',
            'valeurs_paiments',
            'valeurs_salaires'
        ];

        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                \Illuminate\Support\Facades\Log::info("Table {$table} supprimée avec succès.");
            }
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
        // Cette migration supprime des tables, donc la méthode down() ne fait rien
        // car il n'est pas possible de recréer automatiquement les tables supprimées
        // avec leurs données d'origine
    }
};
