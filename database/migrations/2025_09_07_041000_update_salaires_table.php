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
        Schema::table('salaires', function (Blueprint $table) {
            // Suppression des colonnes inutiles
            $table->dropColumn([
                'salaire_base',
                'prime',
                'retenues',
                'net_a_payer',
                'notes',
            ]);
            
            // Renommer les colonnes
            $table->renameColumn('mois_annee', 'periode_debut');
            
            // Ajout des nouvelles colonnes
            $table->date('periode_fin')->after('periode_debut');
            $table->decimal('montant', 10, 2)->after('professeur_id');
            $table->enum('mode_paiement', [
                \App\Models\Paiement::MODE_ESPECES,
                \App\Models\Paiement::MODE_VIREMENT,
                \App\Models\Paiement::MODE_CHEQUE,
                \App\Models\Paiement::MODE_CMI,
            ])->default(\App\Models\Paiement::MODE_ESPECES)->after('date_paiement');
            $table->string('reference_paiement')->nullable()->after('mode_paiement');
            $table->string('preuve_paiement')->nullable()->after('reference_paiement');
            $table->text('commentaire')->nullable()->after('preuve_paiement');
            $table->foreignId('paye_par')->after('commentaire')->nullable()->constrained('users')->onDelete('set null');
            
            // Mise à jour des index
            $table->dropIndex('salaires_mois_annee_index');
            $table->index(['periode_debut', 'periode_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaires', function (Blueprint $table) {
            // Suppression des colonnes ajoutées
            $table->dropForeign(['paye_par']);
            
            $table->dropColumn([
                'periode_fin',
                'montant',
                'mode_paiement',
                'reference_paiement',
                'preuve_paiement',
                'commentaire',
                'paye_par',
            ]);
            
            // Rétablir les colonnes supprimées
            $table->renameColumn('periode_debut', 'mois_annee');
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('prime', 10, 2)->default(0);
            $table->decimal('retenues', 10, 2)->default(0);
            $table->decimal('net_a_payer', 10, 2);
            $table->text('notes')->nullable();
            
            // Rétablir les index
            $table->dropIndex('salaires_periode_debut_periode_fin_index');
            $table->index('mois_annee');
        });
    }
};
