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
        Schema::create('salaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professeur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->string('mois_periode', 7); // Format: YYYY-MM
            $table->integer('nombre_eleves');
            $table->decimal('prix_unitaire', 10, 2); // Prix par élève pour cette matière
            $table->decimal('commission_prof', 5, 2); // Pourcentage commission
            $table->decimal('montant_brut', 10, 2); // nombre_eleves * prix_unitaire
            $table->decimal('montant_commission', 10, 2); // montant_brut * (commission_prof / 100)
            $table->decimal('montant_net', 10, 2); // Montant final à payer
            $table->enum('statut', ['en_attente', 'paye', 'annule'])->default('en_attente');
            $table->date('date_paiement')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour optimiser les requêtes
            $table->index(['professeur_id', 'mois_periode']);
            $table->index(['matiere_id', 'mois_periode']);
            $table->index(['statut', 'mois_periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaires');
    }
};
