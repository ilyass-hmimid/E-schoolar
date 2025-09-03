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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->foreignId('professeur_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['controle', 'devoir', 'examen', 'composition'])->default('controle');
            $table->string('titre', 200);
            $table->text('description')->nullable();
            $table->decimal('note', 5, 2); // Note sur 20 avec 2 décimales
            $table->decimal('coefficient', 3, 1)->default(1.0);
            $table->date('date_evaluation');
            $table->string('trimestre', 20)->nullable(); // 1er trimestre, 2ème trimestre, etc.
            $table->text('commentaires')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour optimiser les requêtes
            $table->index(['etudiant_id', 'matiere_id']);
            $table->index(['professeur_id', 'date_evaluation']);
            $table->index(['trimestre', 'date_evaluation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
