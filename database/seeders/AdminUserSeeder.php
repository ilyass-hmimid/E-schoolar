<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Créer le rôle admin s'il n'existe pas
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );

        // Créer l'utilisateur admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Attribuer le rôle admin
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->command->info('Utilisateur admin créé avec succès!');
        $this->command->info('Email: admin@demo.com');
        $this->command->info('Mot de passe: password');
    }
}
