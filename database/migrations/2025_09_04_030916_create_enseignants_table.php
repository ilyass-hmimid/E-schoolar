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
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('specialite');
            $table->date('date_naissance');
            $table->string('lieu_naissance')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('Maroc');
            $table->string('cin')->unique()->nullable();
            $table->string('carte_sejour')->unique()->nullable();
            $table->string('numero_securite_sociale')->unique()->nullable();
            $table->string('situation_familiale')->nullable();
            $table->integer('nombre_enfants')->default(0);
            $table->string('niveau_etude')->nullable();
            $table->string('diplome')->nullable();
            $table->string('specialite_diplome')->nullable();
            $table->string('etablissement_diplome')->nullable();
            $table->year('annee_obtention_diplome')->nullable();
            $table->decimal('salaire_base', 10, 2)->default(0);
            $table->string('type_contrat')->nullable();
            $table->date('date_embauche');
            $table->date('date_fin_contrat')->nullable();
            $table->string('banque')->nullable();
            $table->string('numero_compte_bancaire')->nullable();
            $table->string('nom_urgence')->nullable();
            $table->string('telephone_urgence')->nullable();
            $table->string('lien_parente_urgence')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};
