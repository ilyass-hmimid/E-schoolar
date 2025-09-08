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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'langue_enseignement')) {
                $table->string('langue_enseignement', 20)->nullable()->after('filiere_id')
                    ->comment('Langue d\'enseignement de l\'élève (arabe, francais, bilingue)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'langue_enseignement')) {
                $table->dropColumn('langue_enseignement');
            }
        });
    }
};
