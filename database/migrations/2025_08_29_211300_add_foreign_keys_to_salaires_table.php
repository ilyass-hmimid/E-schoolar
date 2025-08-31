<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salaires', function (Blueprint $table) {
            // Vérifier les contraintes de clé étrangère existantes
            $foreignKeys = collect(DB::select("SHOW CREATE TABLE salaires"))->first();
            $hasProfFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `salaires_professeur_id_foreign`');
            $hasMatiereFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `salaires_matiere_id_foreign`');
            
            // Ajouter la clé étrangère pour professeur_id si elle n'existe pas
            if (Schema::hasColumn('salaires', 'professeur_id') && !$hasProfFk) {
                $table->foreign('professeur_id', 'salaires_professeur_id_fk')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
            
            // Ajouter la clé étrangère pour matiere_id si elle n'existe pas
            if (Schema::hasColumn('salaires', 'matiere_id') && !$hasMatiereFk) {
                $table->foreign('matiere_id', 'salaires_matiere_id_fk')
                    ->references('id')
                    ->on('matieres')
                    ->onDelete('cascade');
            }
            
            // Ajouter d'autres colonnes et clés étrangères si nécessaire
            if (!Schema::hasColumn('salaires', 'annee_scolaire')) {
                $table->string('annee_scolaire', 9)->after('mois_periode')->nullable();
            }
            
            // Vérifier si niveau_id existe déjà avant de l'ajouter
            if (!Schema::hasColumn('salaires', 'niveau_id')) {
                $table->unsignedBigInteger('niveau_id')->nullable()->after('matiere_id');
            }
            
            // Ajouter la clé étrangère pour niveau_id si elle n'existe pas
            if (!str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `salaires_niveau_id_foreign`')) {
                $table->foreign('niveau_id', 'salaires_niveau_id_fk')
                    ->references('id')
                    ->on('niveaux')
                    ->onDelete('set null');
            }
            
            // Vérifier si les index existent déjà avant de les ajouter
            $indexes = collect(DB::select("SHOW INDEX FROM salaires"))
                ->pluck('Key_name')
                ->toArray();
                
            if (!in_array('salaires_prof_matiere_mois_index', $indexes)) {
                $table->index(
                    ['professeur_id', 'matiere_id', 'mois_periode'], 
                    'salaires_prof_matiere_mois_index'
                );
            }
            
            if (!in_array('salaires_annee_mois_index', $indexes)) {
                $table->index(
                    ['annee_scolaire', 'mois_periode'], 
                    'salaires_annee_mois_index'
                );
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaires', function (Blueprint $table) {
            // Vérifier les contraintes de clé étrangère existantes
            $foreignKeys = collect(DB::select("SHOW CREATE TABLE salaires"))->first();
            
            // Supprimer les contraintes de clé étrangère si elles existent
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `salaires_professeur_id_fk`')) {
                $table->dropForeign('salaires_professeur_id_fk');
            }
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `salaires_matiere_id_fk`')) {
                $table->dropForeign('salaires_matiere_id_fk');
            }
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `salaires_niveau_id_fk`')) {
                $table->dropForeign('salaires_niveau_id_fk');
            }
            
            // Supprimer la colonne niveau_id si elle a été ajoutée
            if (Schema::hasColumn('salaires', 'niveau_id')) {
                $table->dropColumn('niveau_id');
            }
            
            // Supprimer la colonne annee_scolaire si elle a été ajoutée
            if (Schema::hasColumn('salaires', 'annee_scolaire')) {
                $table->dropColumn('annee_scolaire');
            }
            
            // Supprimer les index personnalisés s'ils existent
            $indexes = collect(DB::select("SHOW INDEX FROM salaires"))
                ->pluck('Key_name')
                ->toArray();
                
            if (in_array('salaires_prof_matiere_mois_index', $indexes)) {
                $table->dropIndex('salaires_prof_matiere_mois_index');
            }
            
            if (in_array('salaires_annee_mois_index', $indexes)) {
                $table->dropIndex('salaires_annee_mois_index');
            }
        });
    }
};
