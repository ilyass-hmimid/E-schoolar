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
            // Ajout du statut de justification
            $table->enum('statut_justification', ['en_attente', 'validee', 'rejetee'])
                  ->default('en_attente')
                  ->after('justification');
                  
            // Chemin vers le fichier de pièce jointe
            $table->string('piece_jointe')->nullable()->after('statut_justification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn(['statut_justification', 'piece_jointe']);
        });
    }
};
