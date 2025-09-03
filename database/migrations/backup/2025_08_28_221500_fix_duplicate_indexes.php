<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if SQLite is being used
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the tables without duplicate indexes
            
            // 1. Create a temporary table for notes
            Schema::rename('notes', 'notes_old');
            
            // 2. Recreate the notes table with proper indexes
            Schema::create('notes', function (Blueprint $table) {
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
                
                // Single index on etudiant_id
                $table->index('etudiant_id');
                
                // Other indexes
                $table->index(['etudiant_id', 'matiere_id']);
                $table->index(['professeur_id', 'date_evaluation']);
                $table->index('matiere_id');
                $table->index('type');
                $table->index('trimestre');
                $table->index(['professeur_id', 'date_evaluation', 'created_at']);
            });
            
            // 3. Copy data from old table to new table
            DB::statement('INSERT INTO notes SELECT * FROM notes_old');
            
            // 4. Drop the old table
            Schema::dropIfExists('notes_old');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to implement down as this is a one-time fix
    }
};
