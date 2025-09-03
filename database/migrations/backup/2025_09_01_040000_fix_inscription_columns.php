<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter les colonnes manquantes si elles n'existent pas
        Schema::table('inscriptions', function (Blueprint $table) {
            // Ne pas renommer les colonnes pour éviter les conflits
            // Seulement ajouter les colonnes manquantes
            
            if (!Schema::hasColumn('inscriptions', 'matiere_id')) {
                $table->foreignId('matiere_id')->nullable()->constrained('matieres')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('inscriptions', 'niveau_id')) {
                $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('inscriptions', 'annee_scolaire')) {
                $table->string('annee_scolaire')->nullable();
            }
            
            if (!Schema::hasColumn('inscriptions', 'pack_id')) {
                $table->foreignId('pack_id')->nullable()->constrained('packs')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('inscriptions', 'heures_restantes')) {
                $table->integer('heures_restantes')->nullable();
            }
            
            if (!Schema::hasColumn('inscriptions', 'date_expiration')) {
                $table->date('date_expiration')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas inverser les changements pour éviter des problèmes de données
    }
};
