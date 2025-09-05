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
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained()->onDelete('cascade');
            $table->foreignId('enseignant_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('salle')->nullable();
            $table->enum('statut', ['planifié', 'effectué', 'annulé', 'reporté'])->default('planifié');
            $table->text('contenu')->nullable();
            $table->text('devoirs')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('est_valide')->default(false);
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
