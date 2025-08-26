<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OptimizeIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Index pour la table presences
        if (Schema::hasTable('presences')) {
            Schema::table('presences', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('presences');
                
                // Index simple pour les clés étrangères
                if (!array_key_exists('presences_etudiant_id_index', $indexes)) {
                    $table->index('etudiant_id');
                }
                if (!array_key_exists('presences_matiere_id_index', $indexes)) {
                    $table->index('matiere_id');
                }
                if (!array_key_exists('presences_classe_id_index', $indexes)) {
                    $table->index('classe_id');
                }
                if (!array_key_exists('presences_professeur_id_index', $indexes)) {
                    $table->index('professeur_id');
                }
                
                // Index composé pour les requêtes de recherche par date et statut
                if (!array_key_exists('presences_date_seance_statut_index', $indexes)) {
                    $table->index(['date_seance', 'statut']);
                }
                
                // Index pour les recherches par plage de dates
                if (!array_key_exists('presences_date_seance_index', $indexes)) {
                    $table->index('date_seance');
                }
            });
        }

        // Index pour la table absences
        if (Schema::hasTable('absences')) {
            Schema::table('absences', function (Blueprint $table) use (&$sm) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('absences');
                
                if (!array_key_exists('absences_etudiant_id_index', $indexes)) {
                    $table->index('etudiant_id');
                }
                if (!array_key_exists('absences_matiere_id_index', $indexes)) {
                    $table->index('matiere_id');
                }
                if (!array_key_exists('absences_professeur_id_index', $indexes)) {
                    $table->index('professeur_id');
                }
                if (!array_key_exists('absences_date_absence_index', $indexes)) {
                    $table->index('date_absence');
                }
                if (!array_key_exists('absences_justifiee_index', $indexes)) {
                    $table->index('justifiee');
                }
                if (!array_key_exists('absences_etudiant_id_justifiee_index', $indexes)) {
                    $table->index(['etudiant_id', 'justifiee']);
                }
            });
        }

        // Index pour la table enseignements
        if (Schema::hasTable('enseignements')) {
            Schema::table('enseignements', function (Blueprint $table) use (&$sm) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('enseignements');
                
                if (!array_key_exists('enseignements_professeur_id_index', $indexes)) {
                    $table->index('professeur_id');
                }
                if (!array_key_exists('enseignements_matiere_id_index', $indexes)) {
                    $table->index('matiere_id');
                }
                if (!array_key_exists('enseignements_classe_id_index', $indexes)) {
                    $table->index('classe_id');
                }
                if (!array_key_exists('enseignements_professeur_id_matiere_id_index', $indexes)) {
                    $table->index(['professeur_id', 'matiere_id']);
                }
                if (!array_key_exists('enseignements_matiere_id_classe_id_index', $indexes)) {
                    $table->index(['matiere_id', 'classe_id']);
                }
            });
        }

        // Index pour les tables de relation many-to-many
        if (Schema::hasTable('matiere_user')) {
            Schema::table('matiere_user', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('matiere_user');
                
                if (!array_key_exists('matiere_user_user_id_index', $indexes)) {
                    $table->index('user_id');
                }
                if (!array_key_exists('matiere_user_matiere_id_index', $indexes)) {
                    $table->index('matiere_id');
                }
                if (!array_key_exists('matiere_user_user_id_matiere_id_index', $indexes)) {
                    $table->index(['user_id', 'matiere_id']);
                }
            });
        }

        // Index pour la table etudiants
        if (Schema::hasTable('etudiants')) {
            Schema::table('etudiants', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('etudiants');
                
                if (!array_key_exists('etudiants_classe_id_index', $indexes)) {
                    $table->index('classe_id');
                }
                if (!array_key_exists('etudiants_filiere_id_index', $indexes)) {
                    $table->index('filiere_id');
                }
                if (!array_key_exists('etudiants_niveau_id_index', $indexes)) {
                    $table->index('niveau_id');
                }
                if (!array_key_exists('etudiants_est_actif_index', $indexes)) {
                    $table->index('est_actif');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Suppression des index de la table presences si elle existe
        if (Schema::hasTable('presences')) {
            Schema::table('presences', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('presences');
                
                if (array_key_exists('presences_etudiant_id_index', $indexes)) {
                    $table->dropIndex('presences_etudiant_id_index');
                }
                if (array_key_exists('presences_matiere_id_index', $indexes)) {
                    $table->dropIndex('presences_matiere_id_index');
                }
                if (array_key_exists('presences_classe_id_index', $indexes)) {
                    $table->dropIndex('presences_classe_id_index');
                }
                if (array_key_exists('presences_professeur_id_index', $indexes)) {
                    $table->dropIndex('presences_professeur_id_index');
                }
                if (array_key_exists('presences_date_seance_statut_index', $indexes)) {
                    $table->dropIndex('presences_date_seance_statut_index');
                }
                if (array_key_exists('presences_date_seance_index', $indexes)) {
                    $table->dropIndex('presences_date_seance_index');
                }
            });
        }

        // Suppression des index de la table absences si elle existe
        if (Schema::hasTable('absences')) {
            Schema::table('absences', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('absences');
                
                if (array_key_exists('absences_etudiant_id_index', $indexes)) {
                    $table->dropIndex('absences_etudiant_id_index');
                }
                if (array_key_exists('absences_matiere_id_index', $indexes)) {
                    $table->dropIndex('absences_matiere_id_index');
                }
                if (array_key_exists('absences_professeur_id_index', $indexes)) {
                    $table->dropIndex('absences_professeur_id_index');
                }
                if (array_key_exists('absences_date_absence_index', $indexes)) {
                    $table->dropIndex('absences_date_absence_index');
                }
                if (array_key_exists('absences_justifiee_index', $indexes)) {
                    $table->dropIndex('absences_justifiee_index');
                }
                if (array_key_exists('absences_etudiant_id_justifiee_index', $indexes)) {
                    $table->dropIndex('absences_etudiant_id_justifiee_index');
                }
            });
        }

        // Suppression des index de la table enseignements si elle existe
        if (Schema::hasTable('enseignements')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('enseignements');
                
                if (array_key_exists('enseignements_professeur_id_index', $indexes)) {
                    $table->dropIndex('enseignements_professeur_id_index');
                }
                if (array_key_exists('enseignements_matiere_id_index', $indexes)) {
                    $table->dropIndex('enseignements_matiere_id_index');
                }
                if (array_key_exists('enseignements_classe_id_index', $indexes)) {
                    $table->dropIndex('enseignements_classe_id_index');
                }
                if (array_key_exists('enseignements_professeur_id_matiere_id_index', $indexes)) {
                    $table->dropIndex('enseignements_professeur_id_matiere_id_index');
                }
                if (array_key_exists('enseignements_matiere_id_classe_id_index', $indexes)) {
                    $table->dropIndex('enseignements_matiere_id_classe_id_index');
                }
            });
        }

        // Suppression des index pour les tables de relation
        if (Schema::hasTable('matiere_user')) {
            Schema::table('matiere_user', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('matiere_user');
                
                if (array_key_exists('matiere_user_user_id_index', $indexes)) {
                    $table->dropIndex('matiere_user_user_id_index');
                }
                if (array_key_exists('matiere_user_matiere_id_index', $indexes)) {
                    $table->dropIndex('matiere_user_matiere_id_index');
                }
                if (array_key_exists('matiere_user_user_id_matiere_id_index', $indexes)) {
                    $table->dropIndex('matiere_user_user_id_matiere_id_index');
                }
            });
        }

        // Suppression des index pour la table etudiants si elle existe
        if (Schema::hasTable('etudiants')) {
            Schema::table('etudiants', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('etudiants');
                
                if (array_key_exists('etudiants_classe_id_index', $indexes)) {
                    $table->dropIndex('etudiants_classe_id_index');
                }
                if (array_key_exists('etudiants_filiere_id_index', $indexes)) {
                    $table->dropIndex('etudiants_filiere_id_index');
                }
                if (array_key_exists('etudiants_niveau_id_index', $indexes)) {
                    $table->dropIndex('etudiants_niveau_id_index');
                }
                if (array_key_exists('etudiants_est_actif_index', $indexes)) {
                    $table->dropIndex('etudiants_est_actif_index');
                }
            });
        }
    }
}
