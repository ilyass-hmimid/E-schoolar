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
            $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->enum('type', ['absence', 'retard', 'exclusion'])->default('absence');
            $table->text('motif');
            $table->boolean('est_justifiee')->default(false);
            $table->text('justification')->nullable();
            $table->string('piece_jointe')->nullable();
            $table->enum('statut', ['en_attente', 'validee', 'rejetee'])->default('en_attente');
            $table->foreignId('enregistre_par')->constrained('users');
            $table->foreignId('valide_par')->nullable()->constrained('users');
            $table->timestamp('valide_le')->nullable();
            $table->text('commentaires_validation')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
