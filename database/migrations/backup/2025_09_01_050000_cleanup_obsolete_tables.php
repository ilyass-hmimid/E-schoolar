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
            'professeurs', // Ancienne table remplacée par 'enseignants'
            'etudiant_matiere', // Ancienne relation
            'matiere_professeur', // Ancienne relation
            'salaires_old', // Ancienne table de sauvegarde
            'paiements_old', // Ancienne table de sauvegarde
            'historique_inscriptions', // Ancienne table
            'valeurs_paiments', // Ancienne table
            'valeurs_salaires' // Ancienne table
        ];
        
        // Liste des tables qui ne doivent pas être supprimées car elles sont toujours utilisées
        $protectedTables = [
            'etudiants',
            'enseignants',
            'classes',
            'cours',
            'paiements',
            'absences',
            'notifications',
            'niveaux',
            'filieres',
            'matieres',
            'users',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions'
        ];

        foreach ($tablesToDrop as $table) {
            // Vérifier que la table n'est pas dans la liste des tables protégées
            if (!in_array($table, $protectedTables) && Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                \Illuminate\Support\Facades\Log::info("Table obsolète {$table} supprimée avec succès.");
            } elseif (in_array($table, $protectedTables)) {
                \Illuminate\Support\Facades\Log::info("La table {$table} est protégée et ne sera pas supprimée.");
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
