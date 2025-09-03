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
            // Ajouter le statut de justification s'il n'existe pas
            if (!Schema::hasColumn('absences', 'statut_justification')) {
                $table->enum('statut_justification', ['en_attente', 'validee', 'rejetee'])->default('en_attente')->after('justification');
            }
            
            // Ajouter le champ pour la pièce jointe
            if (!Schema::hasColumn('absences', 'piece_jointe')) {
                $table->string('piece_jointe')->nullable()->after('statut_justification');
            }
            
            // Ajouter un champ pour la date de justification
            if (!Schema::hasColumn('absences', 'date_justification')) {
                $table->timestamp('date_justification')->nullable()->after('piece_jointe');
            }
            
            // Ajouter un champ pour l'utilisateur qui a justifié
            if (!Schema::hasColumn('absences', 'justified_by')) {
                $table->foreignId('justified_by')->nullable()->constrained('users')->onDelete('set null')->after('date_justification');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées si elles existent
            $columns = ['statut_justification', 'piece_jointe', 'date_justification', 'justified_by'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('absences', $column)) {
                    // Supprimer la contrainte de clé étrangère d'abord
                    if ($column === 'justified_by') {
                        $table->dropForeign(['justified_by']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
