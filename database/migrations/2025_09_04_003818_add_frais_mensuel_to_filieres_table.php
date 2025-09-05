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
        Schema::table('filieres', function (Blueprint $table) {
            if (!Schema::hasColumn('filieres', 'frais_mensuel')) {
                $table->decimal('frais_mensuel', 10, 2)->default(0)->after('niveau_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filieres', function (Blueprint $table) {
            if (Schema::hasColumn('filieres', 'frais_mensuel')) {
                $table->dropColumn('frais_mensuel');
            }
        });
    }
};
