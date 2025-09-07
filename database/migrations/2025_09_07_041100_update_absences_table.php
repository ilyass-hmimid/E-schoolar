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
        Schema::table('absences', function (Blueprint $table) {
            // Suppression des contraintes et colonnes inutiles
            $table->dropForeign(['etudiant_id']);
            $table->dropForeign(['matiere_id']);
            $table->dropForeign(['professeur_id']);
            $table->dropForeign(['assistant_id']);
            
            $table->dropColumn([
                'etudiant_id',
                'professeur_id',
                'assistant_id',
                'type',
                'duree_retard',
                'justification',
            ]);
            
            // Renommer les colonnes
            $table->renameColumn('motif', 'raison');
            
            // Ajout des nouvelles colonnes
            $table->foreignId('eleve_id')->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->after('eleve_id')->constrained()->onDelete('cascade');
            $table->enum('statut', [
                'en_attente',
                'justifiee',
                'non_justifiee',
            ])->default('en_attente');
            $table->integer('duree_minutes')->default(60)->comment('Durée de l\'absence en minutes');
            $table->text('commentaire')->nullable()->after('raison');
            $table->foreignId('traite_par')->after('commentaire')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('traite_le')->nullable()->after('traite_par');
            
            // Mise à jour des index
            $table->dropIndex('absences_etudiant_id_date_absence_index');
            $table->index(['eleve_id', 'date_absence']);
            $table->index(['matiere_id', 'date_absence']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            // Suppression des colonnes ajoutées
            $table->dropForeign(['eleve_id']);
            $table->dropForeign(['matiere_id']);
            $table->dropForeign(['traite_par']);
            
            $table->dropColumn([
                'eleve_id',
                'statut',
                'duree_minutes',
                'commentaire',
                'traite_par',
                'traite_le',
            ]);
            
            // Rétablir les colonnes supprimées
            $table->renameColumn('raison', 'motif');
            
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('professeur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assistant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['absence', 'retard', 'sortie_anticipée'])->default('absence');
            $table->integer('duree_retard')->nullable();
            $table->text('justification')->nullable();
            
            // Rétablir les index
            $table->dropIndex('absences_eleve_id_date_absence_index');
            $table->dropIndex('absences_matiere_id_date_absence_index');
            $table->dropIndex('absences_statut_index');
            
            $table->index(['etudiant_id', 'date_absence']);
            $table->index(['matiere_id', 'date_absence']);
        });
    }
};
