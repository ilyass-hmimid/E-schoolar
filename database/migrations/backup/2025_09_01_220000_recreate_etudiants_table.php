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
        // Vérifier si la table existe déjà
        if (!Schema::hasTable('etudiants')) {
            Schema::create('etudiants', function (Blueprint $table) {
                $table->id();
                $table->string('code_etudiant')->unique()->comment('Code unique de l\'étudiant');
                $table->string('nom');
                $table->string('prenom');
                $table->string('email')->unique();
                $table->string('telephone')->nullable();
                $table->string('adresse')->nullable();
                $table->string('ville')->nullable();
                $table->string('pays')->default('Maroc');
                $table->date('date_naissance')->nullable();
                $table->string('lieu_naissance')->nullable();
                $table->string('cin')->nullable()->comment('Carte d\'identité nationale');
                $table->string('cne')->nullable()->comment('Numéro CNE pour les étudiants marocains');
                $table->enum('sexe', ['M', 'F'])->nullable();
                $table->string('photo')->nullable();
                $table->text('notes')->nullable()->comment('Informations complémentaires');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->unsignedBigInteger('classe_id')->nullable();
                $table->unsignedBigInteger('niveau_id')->nullable();
                $table->unsignedBigInteger('filiere_id')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                // Index pour les recherches courantes
                $table->index(['nom', 'prenom']);
                $table->index('code_etudiant');
                $table->index('cne');
                $table->index('classe_id');
                $table->index('niveau_id');
                $table->index('filiere_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas supprimer la table ici pour éviter les problèmes
        // La table sera supprimée par la migration originale si nécessaire
    }
};
