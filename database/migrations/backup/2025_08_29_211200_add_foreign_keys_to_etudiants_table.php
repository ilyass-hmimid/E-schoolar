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
        Schema::table('etudiants', function (Blueprint $table) {
            // Vérifier si les colonnes existent déjà
            $hasUserId = Schema::hasColumn('etudiants', 'user_id');
            $hasClasseId = Schema::hasColumn('etudiants', 'classe_id');
            $hasNiveauId = Schema::hasColumn('etudiants', 'niveau_id');
            $hasFiliereId = Schema::hasColumn('etudiants', 'filiere_id');
            
            // Vérifier les contraintes de clé étrangère existantes
            $foreignKeys = collect(DB::select("SHOW CREATE TABLE etudiants"))->first();
            $hasUserFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_user_id_foreign`');
            $hasClasseFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_classe_id_foreign`');
            $hasNiveauFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_niveau_id_foreign`');
            $hasFiliereFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_filiere_id_foreign`');
            
            // Ajouter user_id si nécessaire
            if (!$hasUserId) {
                $table->foreignId('user_id')->nullable()->after('notes');
            }
            
            // Ajouter les contraintes de clé étrangère si elles n'existent pas
            if (!$hasUserFk) {
                $table->foreign('user_id', 'etudiants_user_id_fk')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
            
            // Ajouter la clé étrangère pour classe_id si elle n'existe pas
            if ($hasClasseId && !$hasClasseFk) {
                $table->foreign('classe_id', 'etudiants_classe_id_fk')
                    ->references('id')
                    ->on('classes')
                    ->onDelete('set null');
            }
            
            // Ajouter niveau_id si nécessaire
            if (!$hasNiveauId) {
                $table->unsignedBigInteger('niveau_id')->nullable()->after('user_id');
            }
            
            // Ajouter la clé étrangère pour niveau_id si elle n'existe pas
            if (!$hasNiveauFk) {
                $table->foreign('niveau_id', 'etudiants_niveau_id_fk')
                    ->references('id')
                    ->on('niveaux')
                    ->onDelete('set null');
            }
            
            // Ajouter filiere_id si nécessaire
            if (!$hasFiliereId) {
                $table->unsignedBigInteger('filiere_id')->nullable()->after('niveau_id');
            }
            
            // Ajouter la clé étrangère pour filiere_id si elle n'existe pas
            if (!$hasFiliereFk) {
                $table->foreign('filiere_id', 'etudiants_filiere_id_fk')
                    ->references('id')
                    ->on('filieres')
                    ->onDelete('set null');
            }
            
            // Vérifier si l'index composite existe déjà
            $indexes = collect(DB::select("SHOW INDEX FROM etudiants"))
                ->pluck('Key_name')
                ->toArray();
                
            if (!in_array('etudiants_user_classe_index', $indexes)) {
                $table->index(['user_id', 'classe_id', 'niveau_id', 'filiere_id'], 'etudiants_user_classe_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            // Supprimer les contraintes de clé étrangère si elles existent
            $foreignKeys = collect(DB::select("SHOW CREATE TABLE etudiants"))->first();
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_user_id_fk`')) {
                $table->dropForeign('etudiants_user_id_fk');
            }
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_classe_id_fk`')) {
                $table->dropForeign('etudiants_classe_id_fk');
            }
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_niveau_id_fk`')) {
                $table->dropForeign('etudiants_niveau_id_fk');
            }
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `etudiants_filiere_id_fk`')) {
                $table->dropForeign('etudiants_filiere_id_fk');
            }
            
            // Supprimer l'index composite s'il existe
            $indexes = collect(DB::select("SHOW INDEX FROM etudiants"))
                ->pluck('Key_name')
                ->toArray();
                
            if (in_array('etudiants_user_classe_index', $indexes)) {
                $table->dropIndex('etudiants_user_classe_index');
            }
            
            // Supprimer les colonnes ajoutées si nécessaire
            if (Schema::hasColumn('etudiants', 'niveau_id')) {
                $table->dropColumn('niveau_id');
            }
            
            if (Schema::hasColumn('etudiants', 'filiere_id')) {
                $table->dropColumn('filiere_id');
            }
            
            // Ne pas supprimer user_id et classe_id car ils sont nécessaires
        });
    }
};
