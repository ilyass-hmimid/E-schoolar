<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Vérifie si un index existe déjà sur la table
     */
    protected function indexExists($table, $name)
    {
        try {
            $connection = Schema::getConnection();
            $dbName = $connection->getDatabaseName();
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$dbName, $table, $name]
            );
            return $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Vérifier et ajouter chaque index s'il n'existe pas déjà
            if (!$this->indexExists('notes', 'idx_notes_prof_date')) {
                $table->index(['professeur_id', 'date_evaluation'], 'idx_notes_prof_date');
            }
            
            if (!$this->indexExists('notes', 'idx_notes_etudiant')) {
                $table->index('etudiant_id', 'idx_notes_etudiant');
            }
            
            if (!$this->indexExists('notes', 'idx_notes_matiere')) {
                $table->index('matiere_id', 'idx_notes_matiere');
            }
            
            if (!$this->indexExists('notes', 'idx_notes_type')) {
                $table->index('type', 'idx_notes_type');
            }
            
            if (!$this->indexExists('notes', 'idx_notes_trimestre')) {
                $table->index('trimestre', 'idx_notes_trimestre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Supprimer uniquement les index que nous avons créés
            $indexes = [
                'idx_notes_prof_date',
                'idx_notes_etudiant',
                'idx_notes_matiere',
                'idx_notes_type',
                'idx_notes_trimestre'
            ];
            
            foreach ($indexes as $index) {
                if ($this->indexExists('notes', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }
};
