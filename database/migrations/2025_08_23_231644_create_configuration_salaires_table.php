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
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->decimal('prix_unitaire', 10, 2)->default(0); // Prix par élève pour cette matière
            $table->decimal('commission_prof', 5, 2)->default(30); // Pourcentage de commission pour le professeur
            $table->decimal('prix_min', 10, 2)->nullable(); // Prix minimum pour cette matière
            $table->decimal('prix_max', 10, 2)->nullable(); // Prix maximum pour cette matière
            $table->boolean('est_actif')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour optimiser les recherches
            $table->index('matiere_id');
            $table->unique('matiere_id'); // Une seule configuration par matière
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
