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
        Schema::create('devoir_eleve', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devoir_id')->constrained()->onDelete('cascade');
            $table->foreignId('eleve_id')->constrained('users')->onDelete('cascade');
            $table->decimal('note', 5, 2)->nullable();
            $table->string('fichier_soumis')->nullable();
            $table->timestamp('date_soumission')->nullable();
            $table->enum('statut', ['non_soumis', 'soumis', 'en_retard', 'corrigé'])->default('non_soumis');
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate entries
            $table->unique(['devoir_id', 'eleve_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoir_eleve');
    }
};
