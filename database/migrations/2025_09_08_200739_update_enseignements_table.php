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
        Schema::table('enseignements', function (Blueprint $table) {
            // Vérifier si la colonne existe déjà avant de l'ajouter
            if (!Schema::hasColumn('enseignements', 'pourcentage_remuneration')) {
                $table->decimal('pourcentage_remuneration', 5, 2)->nullable()
                    ->comment('Pourcentage de rémunération spécifique pour cette association');
            }
            
            if (!Schema::hasColumn('enseignements', 'date_debut')) {
                $table->date('date_debut')->nullable()
                    ->comment('Date de début de l\'enseignement de cette matière');
            }
            
            if (!Schema::hasColumn('enseignements', 'date_fin')) {
                $table->date('date_fin')->nullable()
                    ->comment('Date de fin de l\'enseignement de cette matière (si applicable)');
            }
            
            // Ajout d'index pour les performances (seulement s'ils n'existent pas déjà)
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('enseignements');
            
            if (!array_key_exists('enseignements_professeur_id_matiere_id_index', $indexes)) {
                $table->index(['professeur_id', 'matiere_id']);
            }
            
            if (!array_key_exists('enseignements_date_debut_index', $indexes)) {
                $table->index('date_debut');
            }
            
            if (!array_key_exists('enseignements_date_fin_index', $indexes)) {
                $table->index('date_fin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignements', function (Blueprint $table) {
            // Suppression des colonnes ajoutées
            $table->dropColumn([
                'pourcentage_remuneration',
                'date_debut',
                'date_fin'
            ]);
            
            // Suppression des index
            $table->dropIndex(['professeur_id', 'matiere_id']);
            $table->dropIndex(['date_debut']);
            $table->dropIndex(['date_fin']);
        });
    }
};
