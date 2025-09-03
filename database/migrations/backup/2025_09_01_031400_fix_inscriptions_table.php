<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            // Renommer les colonnes existantes
            if (Schema::hasColumn('inscriptions', 'IdEtudiant')) {
                $table->renameColumn('IdEtudiant', 'etudiant_id');
            }
            if (Schema::hasColumn('inscriptions', 'IdFil')) {
                $table->renameColumn('IdFil', 'filiere_id');
            }
            
            // Ajouter les colonnes manquantes
            if (!Schema::hasColumn('inscriptions', 'matiere_id')) {
                $table->foreignId('matiere_id')->nullable()->constrained('matieres')->onDelete('set null');
            }
            if (!Schema::hasColumn('inscriptions', 'niveau_id')) {
                $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('set null');
            }
            if (!Schema::hasColumn('inscriptions', 'annee_scolaire')) {
                $table->string('annee_scolaire', 9)->nullable();
            }
            if (!Schema::hasColumn('inscriptions', 'date_inscription')) {
                $table->date('date_inscription')->nullable();
            }
            if (!Schema::hasColumn('inscriptions', 'montant')) {
                $table->decimal('montant', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('inscriptions', 'mode_paiement')) {
                $table->string('mode_paiement', 50)->nullable();
            }
            if (!Schema::hasColumn('inscriptions', 'statut')) {
                $table->string('statut', 20)->default('en_attente');
            }
            if (!Schema::hasColumn('inscriptions', 'commentaires')) {
                $table->text('commentaires')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            // Ne pas supprimer les colonnes pour éviter la perte de données
            // Seulement supprimer les contraintes étrangères
            $table->dropForeign(['matiere_id']);
            $table->dropForeign(['niveau_id']);
            
            // Renommer les colonnes à leur nom d'origine si nécessaire
            if (Schema::hasColumn('inscriptions', 'etudiant_id')) {
                $table->renameColumn('etudiant_id', 'IdEtudiant');
            }
            if (Schema::hasColumn('inscriptions', 'filiere_id')) {
                $table->renameColumn('filiere_id', 'IdFil');
            }
        });
    }
};
