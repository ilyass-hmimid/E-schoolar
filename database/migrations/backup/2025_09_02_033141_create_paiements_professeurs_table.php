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
        Schema::create('paiements_professeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professeur_id')->constrained('users')->onDelete('cascade');
            $table->string('mois', 7); // Format: YYYY-MM
            $table->decimal('montant', 10, 2);
            $table->enum('statut', ['paye', 'en_retard', 'impaye'])->default('impaye');
            $table->string('mode_paiement')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->date('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('enregistre_par')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Add composite unique index for professeur_id and mois
            $table->unique(['professeur_id', 'mois']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements_professeurs');
    }
};
