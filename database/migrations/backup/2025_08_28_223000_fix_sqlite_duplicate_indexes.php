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
        if (DB::getDriverName() === 'sqlite') {
            // Drop problematic indexes if they exist
            $this->dropIndexIfExists('notes', 'idx_etudiant');
            $this->dropIndexIfExists('notes', 'idx_matiere');
            $this->dropIndexIfExists('notes', 'idx_type');
            $this->dropIndexIfExists('notes', 'idx_trimestre');
            
            // Recreate the indexes with proper names
            $this->createIndexIfNotExists('notes', 'idx_notes_etudiant', 'etudiant_id');
            $this->createIndexIfNotExists('notes', 'idx_notes_matiere', 'matiere_id');
            $this->createIndexIfNotExists('notes', 'idx_notes_type', 'type');
            $this->createIndexIfNotExists('notes', 'idx_notes_trimestre', 'trimestre');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to implement down as this is a one-time fix
    }

    /**
     * Drop an index if it exists
     */
    protected function dropIndexIfExists(string $table, string $indexName): void
    {
        $indexes = collect(DB::select("PRAGMA index_list({$table})"));
        if ($indexes->contains('name', $indexName)) {
            DB::statement("DROP INDEX IF EXISTS {$indexName}");
        }
    }

    /**
     * Create an index if it doesn't exist
     */
    protected function createIndexIfNotExists(string $table, string $indexName, string|array $columns, ?string $indexType = null): void
    {
        $indexes = collect(DB::select("PRAGMA index_list({$table})"));
        if (!$indexes->contains('name', $indexName)) {
            $columns = is_array($columns) ? implode(',', $columns) : $columns;
            $indexType = $indexType ? strtoupper($indexType) . ' ' : '';
            DB::statement("CREATE {$indexType}INDEX IF NOT EXISTS {$indexName} ON {$table} ({$columns})");
        }
    }
};
