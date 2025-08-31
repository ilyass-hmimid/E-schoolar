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
        Schema::table('users', function (Blueprint $table) {
            // Vérifier si les colonnes existent déjà
            $hasNiveauId = Schema::hasColumn('users', 'niveau_id');
            $hasFiliereId = Schema::hasColumn('users', 'filiere_id');
            
            // Vérifier si les contraintes de clé étrangère existent déjà
            $foreignKeys = collect(DB::select("SHOW CREATE TABLE users"))->first();
            $hasNiveauFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `users_niveau_id_foreign`');
            $hasFiliereFk = str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `users_filiere_id_foreign`');
            
            // Ajouter les colonnes si elles n'existent pas
            if (!$hasNiveauId) {
                $table->unsignedBigInteger('niveau_id')->nullable()->after('address');
            }
            
            if (!$hasFiliereId) {
                $table->unsignedBigInteger('filiere_id')->nullable()->after('niveau_id');
            }
            
            // Ajouter les contraintes de clé étrangère si elles n'existent pas
            if (!$hasNiveauFk) {
                $table->foreign('niveau_id', 'users_niveau_id_fk')
                    ->references('id')
                    ->on('niveaux')
                    ->onDelete('set null');
            }
            
            if (!$hasFiliereFk) {
                $table->foreign('filiere_id', 'users_filiere_id_fk')
                    ->references('id')
                    ->on('filieres')
                    ->onDelete('set null');
            }
            
            // Vérifier si l'index composite existe déjà
            $indexes = collect(DB::select("SHOW INDEX FROM users"))
                ->pluck('Key_name')
                ->toArray();
                
            if (!in_array('users_niveau_filiere_index', $indexes)) {
                $table->index(['niveau_id', 'filiere_id'], 'users_niveau_filiere_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les contraintes de clé étrangère si elles existent
            $foreignKeys = collect(DB::select("SHOW CREATE TABLE users"))->first();
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `users_niveau_id_fk`')) {
                $table->dropForeign('users_niveau_id_fk');
            }
            
            if (str_contains($foreignKeys->{'Create Table'}, 'CONSTRAINT `users_filiere_id_fk`')) {
                $table->dropForeign('users_filiere_id_fk');
            }
            
            // Supprimer l'index composite s'il existe
            $indexes = collect(DB::select("SHOW INDEX FROM users"))
                ->pluck('Key_name')
                ->toArray();
                
            if (in_array('users_niveau_filiere_index', $indexes)) {
                $table->dropIndex('users_niveau_filiere_index');
            }
            
            // Ne pas supprimer les colonnes pour éviter la perte de données
            // $table->dropColumn(['niveau_id', 'filiere_id']);
        });
    }
};
