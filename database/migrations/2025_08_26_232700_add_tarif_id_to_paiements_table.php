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
        Schema::table('paiements', function (Blueprint $table) {
            // Vérifier si la colonne n'existe pas déjà
            if (!Schema::hasColumn('paiements', 'tarif_id')) {
                $table->foreignId('tarif_id')
                    ->after('pack_id')
                    ->nullable()
                    ->constrained('tarifs')
                    ->onDelete('set null');
                
                // Ajouter un index pour optimiser les requêtes
                $table->index('tarif_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (Schema::hasColumn('paiements', 'tarif_id')) {
                // Supprimer la contrainte de clé étrangère
                $table->dropForeign(['tarif_id']);
                // Supprimer l'index
                $table->dropIndex(['tarif_id']);
                // Supprimer la colonne
                $table->dropColumn('tarif_id');
            }
        });
    }
};
