<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Créer l'utilisateur admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'actif',
                'adresse' => 'Adresse de l\'école',
                'telephone' => '0600000000',
                'date_naissance' => Carbon::now()->subYears(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Utilisateur admin créé avec succès!');
        $this->command->info('👤 Email: admin@demo.com');
        $this->command->info('🔑 Mot de passe: password');
    }
}
