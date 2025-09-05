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
            $table->date('mois_annee');
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('prime', 10, 2)->default(0);
            $table->decimal('retenues', 10, 2)->default(0);
            $table->decimal('net_a_payer', 10, 2);
            $table->enum('statut', ['en_attente', 'paye', 'annule'])->default('en_attente');
            $table->date('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('professeur_id');
            $table->index('mois_annee');
            $table->index('statut');
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
