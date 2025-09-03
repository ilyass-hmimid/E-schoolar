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
            // Disable foreign key constraints
            DB::statement('PRAGMA foreign_keys=off');
            
            // Get all indexes on the notes table
            $indexes = DB::select("PRAGMA index_list('notes')");
            
            // Drop all indexes except primary key
            foreach ($indexes as $index) {
                if ($index->name !== 'sqlite_autoindex_notes_1') { // Skip primary key index
                    DB::statement("DROP INDEX IF EXISTS {$index->name}");
                }
            }
            
            // Recreate the necessary indexes with unique names
            Schema::table('notes', function (\Illuminate\Database\Schema\Blueprint $table) {
                // Drop any existing indexes first
                $table->dropIndexIfExists('notes_etudiant_id_matiere_id_index');
                $table->dropIndexIfExists('notes_professeur_id_index');
                $table->dropIndexIfExists('notes_type_index');
                $table->dropIndexIfExists('notes_trimestre_index');
                $table->dropIndexIfExists('notes_date_evaluation_index');
                
                // Recreate indexes with explicit names
                $table->index(['etudiant_id', 'matiere_id'], 'notes_etudiant_matiere_index');
                $table->index('etudiant_id', 'notes_etudiant_id_index');
                $table->index('matiere_id', 'notes_matiere_id_index');
                $table->index('professeur_id', 'notes_professeur_id_index');
                $table->index('type', 'notes_type_index');
                $table->index('trimestre', 'notes_trimestre_index');
                $table->index('date_evaluation', 'notes_date_evaluation_index');
            });
            
            // Re-enable foreign key constraints
            DB::statement('PRAGMA foreign_keys=on');
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
