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
            if (!Schema::hasColumn('filieres', 'duree_annees')) {
                $table->integer('duree_annees')->after('description')->default(1);
            }
            if (!Schema::hasColumn('filieres', 'frais_inscription')) {
                $table->decimal('frais_inscription', 10, 2)->after('duree_annees')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filieres', function (Blueprint $table) {
            if (Schema::hasColumn('filieres', 'duree_annees')) {
                $table->dropColumn('duree_annees');
            }
            if (Schema::hasColumn('filieres', 'frais_inscription')) {
                $table->dropColumn('frais_inscription');
            }
        });
    }
};
