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
        // Vérifier et copier les données des anciennes colonnes vers les nouvelles si elles existent
        $columns = Schema::getColumnListing('inscriptions');
        
        if (in_array('DateInsc', $columns) && in_array('date_inscription', $columns)) {
            \DB::statement("UPDATE inscriptions SET date_inscription = DateInsc WHERE date_inscription IS NULL");
        }
        
        if (in_array('ModePaiement', $columns) && in_array('mode_paiement', $columns)) {
            \DB::statement("UPDATE inscriptions SET mode_paiement = ModePaiement WHERE mode_paiement IS NULL");
        }
        
        if (in_array('Montant', $columns) && in_array('montant', $columns)) {
            \DB::statement("UPDATE inscriptions SET montant = Montant WHERE montant IS NULL");
        }
        
        if (in_array('Statut', $columns) && in_array('statut', $columns)) {
            \DB::statement("UPDATE inscriptions SET statut = Statut WHERE statut IS NULL");
        }
        
        if (in_array('Commentaires', $columns) && in_array('commentaires', $columns)) {
            \DB::statement("UPDATE inscriptions SET commentaires = Commentaires WHERE commentaires IS NULL");
        }

        // Supprimer les anciennes colonnes
        Schema::table('inscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('inscriptions', 'DateInsc')) {
                $table->dropColumn('DateInsc');
            }
            
            if (Schema::hasColumn('inscriptions', 'Montant')) {
                $table->dropColumn('Montant');
            }
            
            if (Schema::hasColumn('inscriptions', 'ModePaiement')) {
                $table->dropColumn('ModePaiement');
            }
            
            if (Schema::hasColumn('inscriptions', 'Statut')) {
                $table->dropColumn('Statut');
            }
            
            if (Schema::hasColumn('inscriptions', 'Commentaires')) {
                $table->dropColumn('Commentaires');
            }
        });

        // Renommer les clés étrangères si nécessaire
        Schema::table('inscriptions', function (Blueprint $table) {
            // Vérifier si la clé étrangère utilise l'ancien nom
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableForeignKeys('inscriptions');
            
            $hasOldForeignKey = false;
            foreach ($indexesFound as $index) {
                if ($index->getName() === 'inscriptions_idetudiant_foreign') {
                    $hasOldForeignKey = true;
                    break;
                }
            }
            
            if ($hasOldForeignKey) {
                // Supprimer l'ancienne contrainte
                $table->dropForeign('inscriptions_idetudiant_foreign');
                $table->dropForeign('inscriptions_idfil_foreign');
                
                // Recréer les contraintes avec les bons noms
                $table->foreign('etudiant_id')
                      ->references('id')->on('users')
                      ->onDelete('cascade');
                      
                $table->foreign('filiere_id')
                      ->references('id')->on('filieres')
                      ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas inverser les changements pour éviter des problèmes de données
    }
};
