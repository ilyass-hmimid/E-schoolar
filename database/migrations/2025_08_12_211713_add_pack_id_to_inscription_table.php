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
        Schema::table('Inscription', function (Blueprint $table) {
            // Ajout de la colonne pack_id comme clé étrangère nullable
            $table->foreignId('pack_id')
                ->after('IdFil')
                ->nullable()
                ->constrained('packs')
                ->nullOnDelete();
                
            // Ajout d'une colonne pour le nombre d'heures restantes
            $table->integer('heures_restantes')
                ->after('pack_id')
                ->default(0);
                
            // Ajout d'une colonne pour la date d'expiration du pack
            $table->date('date_expiration')
                ->after('heures_restantes')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Inscription', function (Blueprint $table) {
            // Suppression de la contrainte de clé étrangère
            $table->dropForeign(['pack_id']);
            
            // Suppression des colonnes ajoutées
            $table->dropColumn(['pack_id', 'heures_restantes', 'date_expiration']);
        });
    }
};
