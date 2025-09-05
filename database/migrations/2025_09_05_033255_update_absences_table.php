<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter les migrations.
     */
    public function up(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            // Renommer la colonne etudiant_id en eleve_id si elle existe
            if (Schema::hasColumn('absences', 'etudiant_id')) {
                $table->renameColumn('etudiant_id', 'eleve_id');
            }

            // Ajouter les colonnes manquantes
            if (!Schema::hasColumn('absences', 'statut')) {
                $table->enum('statut', [
                    \App\Models\Absence::STATUT_JUSTIFIEE,
                    \App\Models\Absence::STATUT_NON_JUSTIFIEE,
                    \App\Models\Absence::STATUT_EN_ATTENTE,
                ])->default(\App\Models\Absence::STATUT_NON_JUSTIFIEE);
            }

            if (!Schema::hasColumn('absences', 'justificatif')) {
                $table->string('justificatif')->nullable()->after('motif');
            }

            if (!Schema::hasColumn('absences', 'duree')) {
                $table->integer('duree')->nullable()->comment('Durée en minutes')->after('justificatif');
            }

            if (!Schema::hasColumn('absences', 'commentaire')) {
                $table->text('commentaire')->nullable()->after('duree');
            }

            if (!Schema::hasColumn('absences', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('absences', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            }

            // Mettre à jour la colonne justifiee pour utiliser le nouveau champ statut
            if (Schema::hasColumn('absences', 'justifiee')) {
                // Mettre à jour les anciennes valeurs
                if (Schema::hasColumn('absences', 'statut')) {
                    \DB::statement("UPDATE absences SET statut = CASE 
                        WHEN justifiee = 1 THEN '" . \App\Models\Absence::STATUT_JUSTIFIEE . "' 
                        ELSE '" . \App\Models\Absence::STATUT_NON_JUSTIFIEE . "' 
                    END");
                }
                
                // Supprimer l'ancienne colonne
                $table->dropColumn('justifiee');
            }

            // Ajouter les index manquants seulement s'ils n'existent pas déjà
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableForeignKeys('absences');
            
            $foreignKeys = collect($indexes)->keyBy(function($index) {
                return $index->getName();
            });
            
            // Vérifier et ajouter la clé étrangère pour eleve_id
            if (!$foreignKeys->has('absences_eleve_id_foreign') && 
                Schema::hasColumn('absences', 'eleve_id') && 
                Schema::hasTable('eleves')) {
                $table->foreign('eleve_id', 'absences_eleve_id_foreign')
                      ->references('id')
                      ->on('eleves')
                      ->onDelete('cascade');
            }
            
            // Vérifier et ajouter la clé étrangère pour matiere_id
            if (!$foreignKeys->has('absences_matiere_id_foreign') && 
                Schema::hasColumn('absences', 'matiere_id') && 
                Schema::hasTable('matieres')) {
                $table->foreign('matiere_id', 'absences_matiere_id_foreign')
                      ->references('id')
                      ->on('matieres')
                      ->onDelete('set null');
            }
            
            // Vérifier et ajouter la clé étrangère pour professeur_id
            if (!$foreignKeys->has('absences_professeur_id_foreign') && 
                Schema::hasColumn('absences', 'professeur_id') && 
                Schema::hasTable('professeurs')) {
                $table->foreign('professeur_id', 'absences_professeur_id_foreign')
                      ->references('id')
                      ->on('professeurs')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            // Ne pas annuler le renommage de la colonne pour éviter les problèmes
            
            // Recréer l'ancienne colonne justifiee
            if (!Schema::hasColumn('absences', 'justifiee')) {
                $table->boolean('justifiee')->default(false);
                
                // Mettre à jour les valeurs basées sur le statut
                \DB::statement("UPDATE absences SET justifiee = CASE 
                    WHEN statut = '" . \App\Models\Absence::STATUT_JUSTIFIEE . "' THEN 1 
                    ELSE 0 
                END");
            }
            
            // Supprimer les colonnes ajoutées
            $columnsToDrop = ['statut', 'justificatif', 'duree', 'commentaire', 'created_by', 'updated_by'];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('absences', $column)) {
                    // Supprimer les contraintes de clé étrangère d'abord
                    if (in_array($column, ['created_by', 'updated_by'])) {
                        $table->dropForeign(['absences_' . $column . '_foreign']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
