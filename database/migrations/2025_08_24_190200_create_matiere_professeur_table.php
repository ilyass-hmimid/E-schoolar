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
        Schema::create('matiere_professeur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_id')->constrained()->onDelete('cascade');
            $table->foreignId('professeur_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Contrainte d'unicité
            $table->unique(['matiere_id', 'professeur_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matiere_professeur');
    }
};
