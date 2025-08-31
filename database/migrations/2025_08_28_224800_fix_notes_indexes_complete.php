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
            // Get all indexes on the notes table
            $indexes = DB::select("PRAGMA index_list('notes')");
            
            // Drop all indexes except primary key
            foreach ($indexes as $index) {
                if ($index->name !== 'sqlite_autoindex_notes_1') {
                    DB::statement("DROP INDEX IF EXISTS {$index->name}");
                }
            }
            
            // Recreate the necessary indexes with proper names
            Schema::table('notes', function (\Illuminate\Database\Schema\Blueprint $table) {
                // Recreate the composite index from the original migration
                if (!Schema::hasIndex('notes', 'notes_etudiant_matiere_index')) {
                    $table->index(['etudiant_id', 'matiere_id'], 'notes_etudiant_matiere_index');
                }
                
                // Recreate the other composite index from the original migration
                if (!Schema::hasIndex('notes', 'notes_professeur_date_index')) {
                    $table->index(['professeur_id', 'date_evaluation'], 'notes_professeur_date_index');
                }
                
                // Recreate the trimestre index from the original migration
                if (!Schema::hasIndex('notes', 'notes_trimestre_date_index')) {
                    $table->index(['trimestre', 'date_evaluation'], 'notes_trimestre_date_index');
                }
                
                // Add other necessary indexes with unique names
                if (!Schema::hasIndex('notes', 'notes_etudiant_id_index')) {
                    $table->index('etudiant_id', 'notes_etudiant_id_index');
                }
                
                if (!Schema::hasIndex('notes', 'notes_matiere_id_index')) {
                    $table->index('matiere_id', 'notes_matiere_id_index');
                }
                
                if (!Schema::hasIndex('notes', 'notes_professeur_id_index')) {
                    $table->index('professeur_id', 'notes_professeur_id_index');
                }
                
                if (!Schema::hasIndex('notes', 'notes_type_index')) {
                    $table->index('type', 'notes_type_index');
                }
                
                if (!Schema::hasIndex('notes', 'notes_trimestre_index')) {
                    $table->index('trimestre', 'notes_trimestre_index');
                }
                
                if (!Schema::hasIndex('notes', 'notes_date_evaluation_index')) {
                    $table->index('date_evaluation', 'notes_date_evaluation_index');
                }
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
};
