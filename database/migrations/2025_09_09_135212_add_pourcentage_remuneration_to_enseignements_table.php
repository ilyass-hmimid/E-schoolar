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
            if (!Schema::hasColumn('enseignements', 'pourcentage_remuneration')) {
                $table->decimal('pourcentage_remuneration', 5, 2)->nullable()->after('nombre_heures_semaine')
                    ->comment('Pourcentage de rémunération spécifique pour ce professeur et cette matière');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignements', function (Blueprint $table) {
            if (Schema::hasColumn('enseignements', 'pourcentage_remuneration')) {
                $table->dropColumn('pourcentage_remuneration');
            }
        });
    }
};
