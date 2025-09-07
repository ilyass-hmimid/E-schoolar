<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Enums\RoleType;
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
        // Récupérer les niveaux et filières existants
        $niveaux = Niveau::pluck('id')->toArray();
        $filieres = Filiere::pluck('id')->toArray();

        // Fonction pour générer un numéro de téléphone aléatoire
        $generatePhone = function() {
            return '06' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        };

        // Création des administrateurs (2)
        $admin1 = User::firstOrCreate(
            ['email' => 'admin1@example.com'],
            [
                'name' => 'Admin',
                'prenom' => 'Principal',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ADMIN->value,
                'phone' => $generatePhone(),
                'is_active' => true,
            ]
        );
        if ($admin1->wasRecentlyCreated) {
            $admin1->is_admin = true;
            $admin1->role = RoleType::ADMIN->value;
            $admin1->save();
        }

        $admin2 = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Admin',
                'prenom' => 'Secondaire',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ADMIN->value,
                'phone' => $generatePhone(),
                'is_active' => true,
            ]
        );
        if ($admin2->wasRecentlyCreated) {
            $admin2->is_admin = true;
            $admin2->role = RoleType::ADMIN->value;
            $admin2->save();
        }

        // Création des professeurs (2)
        $professeur1 = User::firstOrCreate(
            ['email' => 'prof1@example.com'],
            [
                'name' => 'Alami',
                'prenom' => 'Ahmed',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::PROFESSEUR->value,
                'phone' => $generatePhone(),
                'is_active' => true,
            ]
        );
        if ($professeur1->wasRecentlyCreated) {
            $professeur1->is_admin = false;
            $professeur1->role = RoleType::PROFESSEUR->value;
            $professeur1->save();
        }

        $professeur2 = User::firstOrCreate(
            ['email' => 'prof2@example.com'],
            [
                'name' => 'Zahra',
                'prenom' => 'Fatima',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::PROFESSEUR->value,
                'phone' => $generatePhone(),
                'is_active' => true,
            ]
        );
        if ($professeur2->wasRecentlyCreated) {
            $professeur2->is_admin = false;
            $professeur2->role = RoleType::PROFESSEUR->value;
            $professeur2->save();
        }

        // Création des étudiants (2)
        $etudiant1 = User::firstOrCreate(
            ['email' => 'etudiant1@example.com'],
            [
                'name' => 'Benali',
                'prenom' => 'Youssef',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ELEVE->value,
                'phone' => $generatePhone(),
                'address' => '123 Rue des Étudiants, Casablanca 20000',
                'is_active' => true,
            ]
        );
        if ($etudiant1->wasRecentlyCreated) {
            $etudiant1->is_admin = false;
            $etudiant1->role = RoleType::ELEVE->value;
            $etudiant1->save();
        }
        

        $etudiant2 = User::firstOrCreate(
            ['email' => 'etudiant2@example.com'],
            [
                'name' => 'Alaoui',
                'prenom' => 'Leila',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ELEVE->value,
                'phone' => $generatePhone(),
                'address' => '456 Avenue des Étudiantes, Rabat 10000',
                'is_active' => true,
            ]
        );
        if ($etudiant2->wasRecentlyCreated) {
            $etudiant2->is_admin = false;
            $etudiant2->role = RoleType::ELEVE->value;
            $etudiant2->save();
        }

        // Création des assistants (2)
        $assistant1 = User::firstOrCreate(
            ['email' => 'assistant1@example.com'],
            [
                'name' => 'Assistant',
                'prenom' => 'Numero1',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ASSISTANT->value,
                'phone' => $generatePhone(),
                'is_active' => true,
            ]
        );
        if ($assistant1->wasRecentlyCreated) {
            $assistant1->is_admin = false;
            $assistant1->role = RoleType::ASSISTANT->value;
            $assistant1->save();
        }

        $assistant2 = User::firstOrCreate(
            ['email' => 'assistant2@example.com'],
            [
                'name' => 'Assistant',
                'prenom' => 'Numero2',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ASSISTANT->value,
                'phone' => $generatePhone(),
                'is_active' => true,
            ]
        );
        if ($assistant2->wasRecentlyCreated) {
            $assistant2->is_admin = false;
            $assistant2->role = RoleType::ASSISTANT->value;
            $assistant2->save();
        }

        // Désactivation de la création d'utilisateurs supplémentaires pour le moment
        // User::factory(8)->create()->each(function ($user) {
        //     $roles = [
        //         RoleType::ELEVE->value, 
        //         RoleType::PROFESSEUR->value, 
        //         RoleType::ASSISTANT->value
        //     ];
        //     $user->assignRole($roles[array_rand($roles)]);
        // });
    }
}
