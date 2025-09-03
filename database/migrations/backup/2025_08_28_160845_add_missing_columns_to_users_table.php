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
            if (!Schema::hasColumn('users', 'nom')) {
                $table->string('nom')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->nullable()->after('nom');
            }
            
            if (!Schema::hasColumn('users', 'date_naissance')) {
                $table->date('date_naissance')->nullable()->after('prenom');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('users', 'nom')) {
                $columnsToDrop[] = 'nom';
            }
            
            if (Schema::hasColumn('users', 'prenom')) {
                $columnsToDrop[] = 'prenom';
            }
            
            if (Schema::hasColumn('users', 'date_naissance')) {
                $columnsToDrop[] = 'date_naissance';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
