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
            // Rendre les champs optionnels
            $table->string('prenom', 100)->nullable()->change();
            $table->string('telephone', 20)->nullable()->change();
            $table->string('adresse', 255)->nullable()->change();
            $table->date('date_naissance')->nullable()->change();
            $table->string('lieu_naissance', 255)->nullable()->change();
            $table->string('sexe', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas annuler les modifications pour éviter les problèmes
    }
};
