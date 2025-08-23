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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->foreignId('professeur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assistant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_absence');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->enum('type', ['absence', 'retard'])->default('absence');
            $table->integer('duree_retard')->nullable(); // En minutes pour les retards
            $table->text('motif')->nullable();
            $table->boolean('justifiee')->default(false);
            $table->text('justification')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour optimiser les requêtes
            $table->index(['etudiant_id', 'date_absence']);
            $table->index(['matiere_id', 'date_absence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
