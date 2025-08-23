<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\RoleType;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer les utilisateurs de test existants (force delete)
        User::where('email', 'like', '%@example.com')->forceDelete();

        // Créer ou mettre à jour un administrateur
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('password'),
                'role' => RoleType::ADMIN->value,
                'is_active' => true,
            ]
        );

        // Créer ou mettre à jour un professeur
        User::firstOrCreate(
            ['email' => 'prof@example.com'],
            [
                'name' => 'Professeur Test',
                'password' => bcrypt('password'),
                'role' => RoleType::PROFESSEUR->value,
                'is_active' => true,
            ]
        );

        // Créer ou mettre à jour un assistant
        User::firstOrCreate(
            ['email' => 'assistant@example.com'],
            [
                'name' => 'Assistant Test',
                'password' => bcrypt('password'),
                'role' => RoleType::ASSISTANT->value,
                'is_active' => true,
            ]
        );

        // Créer ou mettre à jour un élève
        User::firstOrCreate(
            ['email' => 'etudiant@example.com'],
            [
                'name' => 'Élève Test',
                'password' => bcrypt('password'),
                'role' => RoleType::ELEVE->value,
                'is_active' => true,
            ]
        );

        // Créer ou mettre à jour un utilisateur inactif pour tester
        User::firstOrCreate(
            ['email' => 'inactif@example.com'],
            [
                'name' => 'Utilisateur Inactif',
                'password' => bcrypt('password'),
                'role' => RoleType::ELEVE->value,
                'is_active' => false,
            ]
        );

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Professeur: prof@example.com / password');
        $this->command->info('Assistant: assistant@example.com / password');
        $this->command->info('Élève: etudiant@example.com / password');
        $this->command->info('Inactif: inactif@example.com / password');
    }
}
