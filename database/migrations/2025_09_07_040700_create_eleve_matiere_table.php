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
        Schema::create('eleve_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained()->onDelete('cascade');
            $table->date('date_inscription')->default(now());
            $table->timestamps();
            
            // Assure qu'un élève ne peut s'inscrire qu'une seule fois à une matière
            $table->unique(['eleve_id', 'matiere_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleve_matiere');
    }
};
