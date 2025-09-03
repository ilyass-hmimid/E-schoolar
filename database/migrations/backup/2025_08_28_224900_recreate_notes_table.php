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
            
            // Rename the existing notes table
            if (Schema::hasTable('notes')) {
                Schema::rename('notes', 'notes_old');
            }
            
            // Recreate the notes table with the correct schema and indexes
            Schema::create('notes', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
                $table->foreignId('professeur_id')->constrained('users')->onDelete('cascade');
                $table->enum('type', ['controle', 'devoir', 'examen', 'composition'])->default('controle');
                $table->string('titre', 200);
                $table->text('description')->nullable();
                $table->decimal('note', 5, 2);
                $table->decimal('coefficient', 3, 1)->default(1.0);
                $table->date('date_evaluation');
                $table->string('trimestre', 20)->nullable();
                $table->text('commentaires')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Add indexes with unique names
                $table->index(['etudiant_id', 'matiere_id'], 'notes_etudiant_matiere_index');
                $table->index(['professeur_id', 'date_evaluation'], 'notes_professeur_date_index');
                $table->index(['trimestre', 'date_evaluation'], 'notes_trimestre_date_index');
                $table->index('etudiant_id', 'notes_etudiant_id_index');
                $table->index('matiere_id', 'notes_matiere_id_index');
                $table->index('professeur_id', 'notes_professeur_id_index');
                $table->index('type', 'notes_type_index');
                $table->index('trimestre', 'notes_trimestre_index');
                $table->index('date_evaluation', 'notes_date_evaluation_index');
            });
            
            // Copy data from old table to new table if it exists
            if (Schema::hasTable('notes_old')) {
                $columns = [
                    'id', 'etudiant_id', 'matiere_id', 'professeur_id', 'type', 
                    'titre', 'description', 'note', 'coefficient', 'date_evaluation', 
                    'trimestre', 'commentaires', 'created_at', 'updated_at', 'deleted_at'
                ];
                
                $columnsStr = implode(', ', $columns);
                
                DB::insert("INSERT INTO notes ({$columnsStr}) SELECT {$columnsStr} FROM notes_old");
                
                // Drop the old table
                Schema::dropIfExists('notes_old');
            }
            
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
