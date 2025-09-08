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
        // Vérifier si la colonne 'cne' existe avant de la supprimer
        if (Schema::hasColumn('users', 'cne')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('cne');
            });
        }
        
        // Vérifier si la colonne 'password' existe avant de la supprimer
        if (Schema::hasColumn('users', 'password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la colonne 'cne' si elle a été supprimée
        if (!Schema::hasColumn('users', 'cne')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('cne')->nullable()->after('email');
            });
        }
        
        // Recréer la colonne 'password' si elle a été supprimée
        if (!Schema::hasColumn('users', 'password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('password')->after('cne');
            });
        }
    }
};
