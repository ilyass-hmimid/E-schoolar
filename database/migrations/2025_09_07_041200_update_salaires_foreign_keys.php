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
        Schema::table('salaires', function (Blueprint $table) {
            // Vérifier si les colonnes existent avant de les modifier
            if (!Schema::hasColumn('salaires', 'professeur_id')) {
                $table->foreignId('professeur_id')->after('id')->constrained('users')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('salaires', 'paye_par')) {
                $table->foreignId('paye_par')->after('commentaire')->nullable()->constrained('users')->onDelete('set null');
            }
            
            // Ajouter les index manquants
            $table->index(['professeur_id', 'periode_debut']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaires', function (Blueprint $table) {
            // Supprimer les index
            $table->dropIndex(['professeur_id', 'periode_debut']);
            $table->dropIndex('salaires_statut_index');
            
            // Supprimer les contraintes de clé étrangère
            if (Schema::hasColumn('salaires', 'professeur_id')) {
                $table->dropForeign(['professeur_id']);
            }
            
            if (Schema::hasColumn('salaires', 'paye_par')) {
                $table->dropForeign(['paye_par']);
            }
        });
    }
};
