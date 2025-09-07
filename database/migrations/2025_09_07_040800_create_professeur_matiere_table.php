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
        Schema::create('professeur_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professeur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained()->onDelete('cascade');
            $table->boolean('est_responsable')->default(false);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->timestamps();
            
            // Un professeur ne peut être associé qu'une seule fois à une matière
            $table->unique(['professeur_id', 'matiere_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professeur_matiere');
    }
};
