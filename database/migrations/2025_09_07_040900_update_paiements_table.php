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
        Schema::table('paiements', function (Blueprint $table) {
            // Suppression des contraintes et colonnes inutiles
            $table->dropForeign(['etudiant_id']);
            $table->dropForeign(['matiere_id']);
            $table->dropForeign(['pack_id']);
            $table->dropForeign(['assistant_id']);
            
            $table->dropColumn([
                'etudiant_id',
                'pack_id',
                'assistant_id',
                'mois_periode',
            ]);
            
            // Renommer les colonnes existantes
            $table->renameColumn('commentaires', 'commentaire');
            
            // Ajout des nouvelles colonnes
            $table->foreignId('eleve_id')->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matiere_id')->after('eleve_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                \App\Models\Paiement::TYPE_INSCRIPTION,
                \App\Models\Paiement::TYPE_MENSUALITE,
                \App\Models\Paiement::TYPE_AUTRE,
            ])->default(\App\Models\Paiement::TYPE_MENSUALITE);
            $table->date('mois_couvre')->nullable()->after('date_paiement');
            $table->string('annee_scolaire', 9)->after('mois_couvre');
            $table->string('preuve_paiement')->nullable()->after('reference_paiement');
            $table->foreignId('enregistre_par')->after('preuve_paiement')->constrained('users')->onDelete('set null');
            
            // Mise à jour des index
            $table->dropIndex(['etudiant_id', 'date_paiement']);
            $table->index(['eleve_id', 'date_paiement']);
            $table->index(['matiere_id', 'date_paiement']);
            $table->index(['annee_scolaire', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Suppression des colonnes ajoutées
            $table->dropForeign(['eleve_id']);
            $table->dropForeign(['matiere_id']);
            $table->dropForeign(['enregistre_par']);
            
            $table->dropColumn([
                'eleve_id',
                'type',
                'mois_couvre',
                'annee_scolaire',
                'preuve_paiement',
                'enregistre_par',
            ]);
            
            // Recréer les colonnes supprimées
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pack_id')->nullable()->constrained('packs')->onDelete('set null');
            $table->foreignId('assistant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('mois_periode', 7)->nullable();
            
            // Rétablir les index
            $table->dropIndex(['eleve_id', 'date_paiement']);
            $table->index(['etudiant_id', 'date_paiement']);
        });
    }
};
