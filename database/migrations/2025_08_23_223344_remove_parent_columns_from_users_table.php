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
            // Suppression des colonnes liées aux parents
            $table->dropColumn(['parent_phone', 'parent_email']);
            
            // Déplacer les colonnes après la suppression
            $table->unsignedBigInteger('niveau_id')->nullable()->after('address')->change();
            $table->unsignedBigInteger('filiere_id')->nullable()->after('niveau_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recréer les colonnes supprimées
            $table->string('parent_phone')->nullable()->after('address');
            $table->string('parent_email')->nullable()->after('parent_phone');
            
            // Remettre les colonnes à leur position d'origine
            $table->unsignedBigInteger('niveau_id')->nullable()->after('parent_email')->change();
            $table->unsignedBigInteger('filiere_id')->nullable()->after('niveau_id')->change();
        });
    }
};
