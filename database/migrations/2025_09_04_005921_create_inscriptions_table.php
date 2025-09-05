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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            
            // Additional fields
            $table->string('annee_scolaire', 9); // Format: YYYY-YYYY
            $table->enum('statut', ['inscrit', 'termine', 'abandonne', 'exclu'])->default('inscrit');
            
            // Add composite unique constraint to prevent duplicate entries
            $table->unique(['etudiant_id', 'classe_id', 'annee_scolaire'], 'inscription_unique');
            
            // Indexes for better query performance
            $table->index('etudiant_id');
            $table->index('classe_id');
            $table->index('annee_scolaire');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['etudiant_id']);
            $table->dropForeign(['classe_id']);
            
            // Drop indexes
            $table->dropIndex(['etudiant_id']);
            $table->dropIndex(['classe_id']);
            $table->dropIndex(['annee_scolaire']);
        });
        
        Schema::dropIfExists('inscriptions');
    }
};
