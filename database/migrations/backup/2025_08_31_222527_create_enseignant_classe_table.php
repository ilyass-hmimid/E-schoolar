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
        Schema::create('enseignant_classe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('enseignants')->onDelete('cascade');
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            $table->string('matiere')->nullable();
            $table->boolean('est_principal')->default(false);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();
            
            // Contrainte d'unicité
            $table->unique(['enseignant_id', 'classe_id', 'matiere']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignant_classe');
    }
};
