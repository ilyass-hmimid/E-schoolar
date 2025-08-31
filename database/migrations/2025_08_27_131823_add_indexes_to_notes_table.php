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
        Schema::table('notes', function (Blueprint $table) {
            // Index composite pour les requêtes de liste triées par date
            $table->index(['professeur_id', 'date_evaluation', 'created_at'], 'idx_prof_date_created');
            
            // Index pour les recherches par étudiant
            $table->index('etudiant_id', 'idx_etudiant');
            
            // Index pour les recherches par matière
            $table->index('matiere_id', 'idx_matiere');
            
            // Index pour les recherches par type de note
            $table->index('type', 'idx_type');
            
            // Index pour les recherches par trimestre
            $table->index('trimestre', 'idx_trimestre');
            
            // Index pour les recherches par plage de dates
            $table->index('date_evaluation', 'idx_date_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex('idx_prof_date_created');
            $table->dropIndex('idx_etudiant');
            $table->dropIndex('idx_matiere');
            $table->dropIndex('idx_type');
            $table->dropIndex('idx_trimestre');
            $table->dropIndex('idx_date_evaluation');
        });
    }
};
