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
        // Vérifier si la table des étudiants existe
        if (!Schema::hasTable('etudiants')) {
            throw new \Exception('La table etudiants doit être créée avant la table presences');
        }
        
        // Vérifier si la table des classes existe
        if (!Schema::hasTable('classes')) {
            throw new \Exception('La table classes doit être créée avant la table presences');
        }
        
        // Vérifier si la table des matières existe
        if (!Schema::hasTable('matieres')) {
            throw new \Exception('La table matieres doit être créée avant la table presences');
        }
        
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            
            // Clés étrangères avec vérification d'existence
            $table->unsignedBigInteger('etudiant_id');
            $table->foreign('etudiant_id')
                  ->references('id')
                  ->on('etudiants')
                  ->onDelete('cascade');
                  
            $table->unsignedBigInteger('professeur_id');
            $table->foreign('professeur_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->unsignedBigInteger('matiere_id');
            $table->foreign('matiere_id')
                  ->references('id')
                  ->on('matieres')
                  ->onDelete('cascade');
                  
            $table->unsignedBigInteger('classe_id');
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('cascade');
            
            $table->date('date_seance');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->enum('statut', ['present', 'absent', 'retard', 'justifie']);
            $table->integer('duree_retard')->nullable()->comment('Durée du retard en minutes');
            $table->string('justificatif')->nullable();
            $table->text('commentaire')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Index pour les recherches courantes
            $table->index(['date_seance', 'statut']);
            $table->index(['etudiant_id', 'matiere_id']);
            $table->index(['professeur_id', 'date_seance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
