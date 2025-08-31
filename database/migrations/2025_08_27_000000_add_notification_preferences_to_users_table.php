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
            $table->json('notification_preferences')
                ->after('remember_token')
                ->nullable()
                ->comment('Préférences de notification de l\'utilisateur');
        });

        // Définir les préférences de notification par défaut pour les utilisateurs existants
        \DB::table('users')->update([
            'notification_preferences' => json_encode([
                'email' => true,
                'database' => true,
                'sms' => false,
                'push' => false,
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notification_preferences');
        });
    }
};
