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
        Schema::table('eleves', function (Blueprint $table) {
            // Ajouter la colonne user_id si elle n'existe pas déjà
            if (!Schema::hasColumn('eleves', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            // Supprimer les colonnes qui seront gérées par la table users
            $columnsToDrop = ['nom', 'prenom', 'email', 'telephone', 'adresse'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('eleves', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // Supprimer la clé étrangère et la colonne user_id
            if (Schema::hasColumn('eleves', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            // Recréer les colonnes supprimées
            $table->string('nom')->after('id');
            $table->string('prenom')->after('nom');
            $table->string('email')->unique()->after('prenom');
            $table->string('telephone')->nullable()->after('email');
            $table->string('adresse')->nullable()->after('telephone');
        });
    }
};
