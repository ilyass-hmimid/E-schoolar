<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetupDatabase extends Migration
{
    public function up()
    {
        // Drop all existing tables
        $tables = DB::select('SHOW TABLES');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            Schema::dropIfExists($tableName);
        }
        
        // Create tables in correct order
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });

        Schema::create('filieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });

        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('prix', 10, 2);
            $table->integer('duree_jours')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('enseignements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->date('date_absence');
            $table->boolean('est_justifiee')->default(false);
            $table->text('justification')->nullable();
            $table->timestamps();
        });

        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('pack_id')->nullable()->constrained('packs')->onDelete('set null');
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->string('mode_paiement');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // This migration cannot be reversed
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        $tables = [
            'paiements',
            'absences',
            'enseignements',
            'etudiants',
            'users',
            'packs',
            'matieres',
            'filieres',
            'niveaux',
        ];
        
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
