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
            $table->foreignId('classe_id')->after('filiere_id')->nullable()->constrained('classes')->onDelete('cascade');
            
            // Update the unique constraint to include the new classe_id
            $table->dropUnique('unique_enseignement');
            $table->unique(['professeur_id', 'matiere_id', 'niveau_id', 'filiere_id', 'classe_id'], 'unique_enseignement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignements', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['classe_id']);
            
            // Drop the unique constraint with the new columns
            $table->dropUnique('unique_enseignement');
            
            // Re-add the original unique constraint
            $table->unique(['professeur_id', 'matiere_id', 'niveau_id', 'filiere_id'], 'unique_enseignement');
            
            // Finally drop the column
            $table->dropColumn('classe_id');
        });
    }
};
