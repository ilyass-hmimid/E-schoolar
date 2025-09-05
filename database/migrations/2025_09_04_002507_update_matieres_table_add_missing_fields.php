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
        Schema::table('matieres', function (Blueprint $table) {
            // Add prix (tarif par élève)
            if (!Schema::hasColumn('matieres', 'prix')) {
                $table->decimal('prix', 10, 2)->after('code');
            }
            
            // Add prix_prof (optionnel)
            if (!Schema::hasColumn('matieres', 'prix_prof')) {
                $table->decimal('prix_prof', 10, 2)->nullable()->after('prix');
            }
            
            // Add type (pour catégorisation)
            if (!Schema::hasColumn('matieres', 'type')) {
                $table->string('type')->nullable()->after('prix_prof');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->dropColumn(['prix', 'prix_prof', 'type']);
        });
    }
};
