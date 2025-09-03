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
        Schema::table('classes', function (Blueprint $table) {
            // Ajouter la colonne niveau_id comme nullable d'abord
            $table->foreignId('niveau_id')->nullable()->after('id')->constrained('niveaux')->onDelete('set null');
            
            // Créer un index pour améliorer les performances des requêtes
            $table->index('niveau_id');
        });
        
        // Mettre à jour les enregistrements existants avec un niveau par défaut si nécessaire
        // Cette partie nécessiterait une logique supplémentaire en fonction de votre domaine métier
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère d'abord
            $table->dropForeign(['niveau_id']);
            
            // Puis supprimer la colonne
            $table->dropColumn('niveau_id');
        });
    }
};
