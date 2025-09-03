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
        // Renommer les colonnes dans la table enseignements si elles existent
        if (Schema::hasColumn('enseignements', 'IdProf')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('IdProf', 'professeur_id');
            });
        }

        if (Schema::hasColumn('enseignements', 'IdMat')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('IdMat', 'matiere_id');
            });
        }

        if (Schema::hasColumn('enseignements', 'IdNiv')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('IdNiv', 'niveau_id');
            });
        }

        if (Schema::hasColumn('enseignements', 'IdFil')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('IdFil', 'filiere_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux anciens noms de colonnes si nécessaire
        if (Schema::hasColumn('enseignements', 'professeur_id')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('professeur_id', 'IdProf');
            });
        }

        if (Schema::hasColumn('enseignements', 'matiere_id')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('matiere_id', 'IdMat');
            });
        }

        if (Schema::hasColumn('enseignements', 'niveau_id')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('niveau_id', 'IdNiv');
            });
        }

        if (Schema::hasColumn('enseignements', 'filiere_id')) {
            Schema::table('enseignements', function (Blueprint $table) {
                $table->renameColumn('filiere_id', 'IdFil');
            });
        }
    }
};
