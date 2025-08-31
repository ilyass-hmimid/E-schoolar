<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création de l'administrateur
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $admin->assignRole('admin');

        // Création d'un professeur
        $professeur = User::create([
            'name' => 'Professeur Test',
            'email' => 'professeur@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $professeur->assignRole('professeur');

        // Création d'un assistant
        $assistant = User::create([
            'name' => 'Assistant Test',
            'email' => 'assistant@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $assistant->assignRole('assistant');

        // Création d'un élève
        $eleve = User::create([
            'name' => 'Élève Test',
            'email' => 'eleve@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $eleve->assignRole('eleve');

        // Création de quelques utilisateurs supplémentaires pour les tests
        User::factory(5)->create()->each(function ($user) {
            $roles = ['eleve', 'professeur', 'assistant'];
            $user->assignRole($roles[array_rand($roles)]);
        });
    }
}
