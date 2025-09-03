<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Drop problematic indexes if they exist
            $this->dropIndexIfExists('notes', 'idx_etudiant');
            
            // Recreate the indexes with proper names if they don't exist
            $this->createIndexIfNotExists('notes', 'idx_notes_etudiant', 'etudiant_id');
            
            // Also check and fix any other potential duplicate indexes
            $this->dropIndexIfExists('notes', 'idx_matiere');
            $this->createIndexIfNotExists('notes', 'idx_notes_matiere', 'matiere_id');
            
            $this->dropIndexIfExists('notes', 'idx_type');
            $this->createIndexIfNotExists('notes', 'idx_notes_type', 'type');
            
            $this->dropIndexIfExists('notes', 'idx_trimestre');
            $this->createIndexIfNotExists('notes', 'idx_notes_trimestre', 'trimestre');
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
        $indexes = collect(DB::select("PRAGMA index_list({$table})"));
        if ($indexes->contains('name', $indexName)) {
            DB::statement("DROP INDEX IF EXISTS {$indexName}");
        }
    }

    /**
     * Create an index if it doesn't exist (SQLite specific)
     */
    protected function createIndexIfNotExists(string $table, string $indexName, string $column): void
    {
        $indexes = collect(DB::select("PRAGMA index_list({$table})"));
        if (!$indexes->contains('name', $indexName)) {
            DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$column})");
        }
    }
};
