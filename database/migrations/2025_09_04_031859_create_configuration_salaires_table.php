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
        Schema::create('configuration_salaires', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Ex: 'prime_anciennete', 'prime_rendement', 'taux_cnss', 'ir', etc.
            $table->string('libelle');
            $table->decimal('valeur', 10, 2);
            $table->string('unite')->default('pourcentage'); // pourcentage, montant_fixe
            $table->text('description')->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->json('conditions')->nullable(); // Pour des conditions plus complexes
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour les recherches par type et par période
            $table->index(['type', 'date_debut', 'date_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_salaires');
    }
};
