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
        Schema::table('enseignements', function (Blueprint $table) {
            // Add classe_id column if it doesn't exist
            if (!Schema::hasColumn('enseignements', 'classe_id')) {
                $table->unsignedBigInteger('classe_id')->after('filiere_id');
            }
            
            // Add annee_scolaire column if it doesn't exist
            if (!Schema::hasColumn('enseignements', 'annee_scolaire')) {
                $table->string('annee_scolaire', 9)->default('2024-2025')->after('classe_id');
            }
            
            // Add foreign key constraint for classe_id if it doesn't exist
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys('enseignements');
            $fkExists = false;
            
            foreach ($foreignKeys as $fk) {
                if ($fk->getLocalColumns() === ['classe_id']) {
                    $fkExists = true;
                    break;
                }
            }
            
            if (!$fkExists) {
                $table->foreign('classe_id')
                      ->references('id')
                      ->on('classes')
                      ->onDelete('cascade');
            }
            
            // Add composite unique constraint if it doesn't exist
            $indexes = $sm->listTableIndexes('enseignements');
            
            if (!isset($indexes['enseignement_unique'])) {
                $table->unique(
                    ['matiere_id', 'professeur_id', 'classe_id', 'annee_scolaire'],
                    'enseignement_unique'
                );
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignements', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['classe_id']);
            
            // Drop unique constraint if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('enseignements');
            
            if (isset($indexes['enseignement_unique'])) {
                $table->dropUnique('enseignement_unique');
            }
        });
    }
};
