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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade');
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');
            
            // Class identification
            $table->string('nom', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            
            // Capacity and status
            $table->unsignedSmallInteger('capacite_max')->default(30);
            $table->boolean('est_actif')->default(true);
            
            // Academic year this class is for (e.g., 2024-2025)
            $table->string('annee_scolaire', 9);
            
            // Add composite unique constraint
            $table->unique(['code', 'annee_scolaire'], 'classe_annee_unique');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
