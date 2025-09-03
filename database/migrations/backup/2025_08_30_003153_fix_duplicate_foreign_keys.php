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
        Schema::table('absences', function (Blueprint $table) {
            // Supprimer les contraintes de clé étrangère en double
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableForeignKeys('absences');
            
            $foreignKeys = [];
            foreach ($indexes as $index) {
                $foreignKeys[$index->getName()] = [
                    'columns' => $index->getColumns(),
                    'on' => $index->getForeignTableName(),
                    'references' => $index->getForeignColumns(),
                    'onDelete' => $index->hasOption('onDelete') ? $index->getOption('onDelete') : null,
                ];
            }
            
            // Supprimer toutes les contraintes existantes
            foreach ($foreignKeys as $name => $constraint) {
                $table->dropForeign($name);
            }
            
            // Recréer les contraintes nécessaires sans doublons
            $table->foreign('etudiant_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
                
            $table->foreign('matiere_id')
                ->references('id')
                ->on('matieres')
                ->onDelete('cascade');
                
            $table->foreign('professeur_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
                
            $table->foreign('assistant_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette opération est potentiellement destructrice, donc on ne fait rien en reverse
        // car on ne peut pas savoir quelles étaient les contraintes d'origine
    }
};
