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
            // Ajouter created_by et updated_by s'ils n'existent pas
            if (!Schema::hasColumn('paiements', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('commentaires');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('paiements', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            }

            // Modifier les colonnes existantes si nécessaire
            if (Schema::hasColumn('paiements', 'mois_periode')) {
                $table->date('mois_periode')->nullable()->change();
            }

            // Ajouter un index sur eleve_id pour les performances
            $table->index('eleve_id');
            $table->index('date_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Supprimer les clés étrangères
            if (Schema::hasColumn('paiements', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            if (Schema::hasColumn('paiements', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }

            // Supprimer les index
            $table->dropIndex(['eleve_id']);
            $table->dropIndex(['date_paiement']);
        });
    }
};
