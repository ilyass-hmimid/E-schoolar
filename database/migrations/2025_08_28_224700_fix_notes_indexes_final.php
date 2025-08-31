<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run for SQLite
        if (DB::getDriverName() === 'sqlite') {
            // Drop all existing indexes on notes table
            $this->dropAllIndexesOnTable('notes');
            
            // Recreate the indexes with proper names
            Schema::table('notes', function (\Illuminate\Database\Schema\Blueprint $table) {
                // Recreate the composite index from the original migration
                $table->index(['etudiant_id', 'matiere_id'], 'notes_etudiant_matiere_index');
                
                // Recreate the other composite index from the original migration
                $table->index(['professeur_id', 'date_evaluation'], 'notes_professeur_date_index');
                
                // Recreate the trimestre index from the original migration
                $table->index(['trimestre', 'date_evaluation'], 'notes_trimestre_date_index');
                
                // Add other necessary indexes with unique names
                $table->index('etudiant_id', 'notes_etudiant_id_index');
                $table->index('matiere_id', 'notes_matiere_id_index');
                $table->index('professeur_id', 'notes_professeur_id_index');
                $table->index('type', 'notes_type_index');
                $table->index('trimestre', 'notes_trimestre_index');
                $table->index('date_evaluation', 'notes_date_evaluation_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible as this is a one-time fix
    }
    
    /**
     * Drop all indexes on a table (SQLite specific)
     */
    protected function dropAllIndexesOnTable(string $tableName): void
    {
        $indexes = DB::select("PRAGMA index_list('{$tableName}')");
        
        foreach ($indexes as $index) {
            // Skip primary key index
            if ($index->name === 'sqlite_autoindex_' . $tableName . '_1') {
                continue;
            }
            
            DB::statement("DROP INDEX IF EXISTS {$index->name}");
        }
    }
};
