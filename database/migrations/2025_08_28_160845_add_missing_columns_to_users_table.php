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
            $table->string('nom')->nullable()->after('name');
            $table->string('prenom')->nullable()->after('nom');
            $table->string('parent_name')->nullable()->after('prenom');
            $table->string('parent_phone', 20)->nullable()->after('parent_name');
            $table->date('date_naissance')->nullable()->after('parent_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nom',
                'prenom',
                'parent_name',
                'parent_phone',
                'date_naissance'
            ]);
        });
    }
};
