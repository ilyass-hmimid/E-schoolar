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
        Schema::table('absences', function (Blueprint $table) {
            // Index composite pour les requêtes de liste triées par date
            $table->index(['professeur_id', 'date_absence', 'heure_debut'], 'idx_prof_date_heure');
            
            // Index pour les recherches par étudiant
            $table->index('etudiant_id', 'idx_etudiant');
            
            // Index pour les recherches par matière
            $table->index('matiere_id', 'idx_matiere');
            
            // Index pour les filtres sur le statut de justification
            $table->index('justifiee', 'idx_justifiee');
            
            // Index pour les recherches par plage de dates
            $table->index('date_absence', 'idx_date_absence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropIndex('idx_prof_date_heure');
            $table->dropIndex('idx_etudiant');
            $table->dropIndex('idx_matiere');
            $table->dropIndex('idx_justifiee');
            $table->dropIndex('idx_date_absence');
        });
    }
};
