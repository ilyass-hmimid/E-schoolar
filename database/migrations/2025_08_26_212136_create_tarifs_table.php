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
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained()->onDelete('cascade');
            $table->foreignId('niveau_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('filiere_id')->nullable()->constrained()->onDelete('set null');
            
            $table->decimal('montant', 10, 2);
            $table->enum('type_tarif', ['standard', 'promotionnel', 'groupe', 'premium'])->default('standard');
            $table->date('periode_validite_debut')->nullable();
            $table->date('periode_validite_fin')->nullable();
            $table->boolean('est_actif')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches courantes
            $table->index(['pack_id', 'niveau_id', 'filiere_id'], 'tarif_search_index');
            $table->index(['est_actif', 'type_tarif'], 'tarif_status_index');
        });

        // Ajout de la clé étrangère tarif_id à la table paiements
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('tarif_id')->nullable()->after('pack_id')
                  ->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression de la clé étrangère tarif_id de la table paiements
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['tarif_id']);
            $table->dropColumn('tarif_id');
        });

        Schema::dropIfExists('tarifs');
    }
};
