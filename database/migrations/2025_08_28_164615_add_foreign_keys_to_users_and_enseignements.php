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
        // Use raw SQL to check and add foreign keys if they don't exist
        $foreignKeys = [
            ['table' => 'users', 'column' => 'niveau_id', 'references' => 'niveaux', 'onDelete' => 'SET NULL'],
            ['table' => 'users', 'column' => 'filiere_id', 'references' => 'filieres', 'onDelete' => 'SET NULL'],
            ['table' => 'enseignements', 'column' => 'professeur_id', 'references' => 'users', 'onDelete' => 'CASCADE'],
            ['table' => 'enseignements', 'column' => 'matiere_id', 'references' => 'matieres', 'onDelete' => 'CASCADE'],
        ];

        foreach ($foreignKeys as $fk) {
            $constraintName = "{$fk['table']}_{$fk['column']}_foreign";
            
            $exists = \DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE CONSTRAINT_SCHEMA = DATABASE() 
                AND TABLE_NAME = '{$fk['table']}' 
                AND CONSTRAINT_NAME = '{$constraintName}'
            ");

            if ($exists[0]->count == 0) {
                \DB::statement("
                    ALTER TABLE `{$fk['table']}` 
                    ADD CONSTRAINT `{$constraintName}` 
                    FOREIGN KEY (`{$fk['column']}`) 
                    REFERENCES `{$fk['references']}`(id) 
                    ON DELETE {$fk['onDelete']}
                    ON UPDATE CASCADE
                ");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // List of foreign keys to drop
        $foreignKeys = [
            ['table' => 'users', 'column' => 'niveau_id'],
            ['table' => 'users', 'column' => 'filiere_id'],
            ['table' => 'enseignements', 'column' => 'professeur_id'],
            ['table' => 'enseignements', 'column' => 'matiere_id'],
        ];

        foreach ($foreignKeys as $fk) {
            $constraintName = "{$fk['table']}_{$fk['column']}_foreign";
            
            $exists = \DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE CONSTRAINT_SCHEMA = DATABASE() 
                AND TABLE_NAME = '{$fk['table']}' 
                AND CONSTRAINT_NAME = '{$constraintName}'
            ");

            if ($exists[0]->count > 0) {
                \DB::statement("ALTER TABLE `{$fk['table']}` DROP FOREIGN KEY `{$constraintName}`");
            }
        }
    }
};
