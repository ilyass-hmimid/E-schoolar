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
        Schema::create('paiements_prof', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professeur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->integer('nbre_eleves');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('pourcentage', 5, 2);
            $table->decimal('montant_prof', 10, 2);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['en_attente', 'paye', 'annule'])->default('en_attente');
            $table->date('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['professeur_id', 'date_debut', 'date_fin']);
            $table->index(['matiere_id', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements_prof');
    }
};
