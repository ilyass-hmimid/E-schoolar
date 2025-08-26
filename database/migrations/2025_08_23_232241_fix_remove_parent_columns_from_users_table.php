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
        // Vérifier si les colonnes existent avant de les supprimer
        $schema = DB::getDoctrineSchemaManager();
        $table = $schema->listTableDetails('users');
        
        // Supprimer parent_phone s'il existe
        if ($table->hasColumn('parent_phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('parent_phone');
            });
        }
        
        // Supprimer parent_email s'il existe
        if ($table->hasColumn('parent_email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('parent_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
