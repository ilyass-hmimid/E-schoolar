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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->foreignId('pack_id')->nullable()->constrained('packs')->onDelete('set null');
            $table->foreignId('assistant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('montant', 10, 2);
            $table->enum('mode_paiement', ['especes', 'cheque', 'virement', 'carte'])->default('especes');
            $table->string('reference_paiement')->nullable();
            $table->date('date_paiement');
            $table->enum('statut', ['en_attente', 'valide', 'annule'])->default('en_attente');
            $table->text('commentaires')->nullable();
            $table->string('mois_periode', 7)->nullable(); // Format: YYYY-MM
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour optimiser les requêtes
            $table->index(['etudiant_id', 'date_paiement']);
            $table->index(['matiere_id', 'date_paiement']);
            $table->index(['statut', 'date_paiement']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
