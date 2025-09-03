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
            // Drop the problematic index if it exists
            $this->dropIndexIfExists('notes', 'idx_etudiant');
            
            // Create the correct index if it doesn't exist
            if (!Schema::hasColumn('notes', 'etudiant_id')) {
                return;
            }
            
            // Check if the correct index already exists
            $indexes = DB::select("PRAGMA index_list('notes')");
            $hasCorrectIndex = false;
            
            foreach ($indexes as $index) {
                if ($index->name === 'notes_etudiant_id_index') {
                    $hasCorrectIndex = true;
                    break;
                }
            }
            
            if (!$hasCorrectIndex) {
                DB::statement('CREATE INDEX IF NOT EXISTS notes_etudiant_id_index ON notes(etudiant_id)');
            }
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
     * Drop an index if it exists (SQLite specific)
     */
    protected function dropIndexIfExists(string $table, string $indexName): void
    {
        $indexes = DB::select("PRAGMA index_list('{$table}')");
        
        foreach ($indexes as $index) {
            if ($index->name === $indexName) {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
                break;
            }
        }
    }
};
