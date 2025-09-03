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
        Schema::table('classes', function (Blueprint $table) {
            // Vérifier si la colonne existe avant de tenter de la supprimer
            if (Schema::hasColumn('classes', 'niveau')) {
                $table->dropColumn('niveau');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Recréer la colonne niveau en cas de rollback
            $table->string('niveau')->nullable()->after('nom');
        });
    }
};
