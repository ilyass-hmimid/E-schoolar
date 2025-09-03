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
                'name' => 'Admin Principal',
                'nom' => 'Admin',
                'prenom' => 'Principal',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ADMIN->value,
                'phone' => $generatePhone(),
                'address' => '123 Rue Admin, Casablanca 20000, Maroc',
                'is_active' => true,
            ]
        );
        if ($admin1->wasRecentlyCreated) {
            $admin1->assignRole('admin');
        }

        $admin2 = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Admin Secondaire',
                'nom' => 'Admin',
                'prenom' => 'Secondaire',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ADMIN->value,
                'phone' => $generatePhone(),
                'address' => '456 Avenue Admin, Rabat 10000, Maroc',
                'is_active' => true,
            ]
        );
        if ($admin2->wasRecentlyCreated) {
            $admin2->assignRole('admin');
        }

        // Création des professeurs (2)
        $professeur1 = User::firstOrCreate(
            ['email' => 'prof1@example.com'],
            [
                'name' => 'Ahmed Alami',
                'nom' => 'Alami',
                'prenom' => 'Ahmed',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::PROFESSEUR->value,
                'phone' => $generatePhone(),
                'address' => '789 Rue des Professeurs, Casablanca 20000',
                'is_active' => true,
                'niveau_id' => !empty($niveaux) ? $niveaux[array_rand($niveaux)] : null,
                'filiere_id' => !empty($filieres) ? $filieres[array_rand($filieres)] : null,
            ]
        );
        if ($professeur1->wasRecentlyCreated) {
            $professeur1->assignRole('professeur');
        }

        $professeur2 = User::firstOrCreate(
            ['email' => 'prof2@example.com'],
            [
                'name' => 'Fatima Zahra',
                'nom' => 'Zahra',
                'prenom' => 'Fatima',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::PROFESSEUR->value,
                'phone' => $generatePhone(),
                'address' => '101 Avenue des Enseignants, Rabat 10000',
                'is_active' => true,
                'niveau_id' => !empty($niveaux) ? $niveaux[array_rand($niveaux)] : null,
                'filiere_id' => !empty($filieres) ? $filieres[array_rand($filieres)] : null,
            ]
        );
        if ($professeur2->wasRecentlyCreated) {
            $professeur2->assignRole('professeur');
        }

        // Création des étudiants (2)
        $etudiant1 = User::firstOrCreate(
            ['email' => 'etudiant1@example.com'],
            [
                'name' => 'Youssef Benali',
                'nom' => 'Benali',
                'prenom' => 'Youssef',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ELEVE->value,
                'phone' => $generatePhone(),
                'address' => '123 Rue des Étudiants, Casablanca 20000',
                'date_naissance' => now()->subYears(rand(18, 25))->format('Y-m-d'),
                'niveau_id' => !empty($niveaux) ? $niveaux[array_rand($niveaux)] : null,
                'filiere_id' => !empty($filieres) ? $filieres[array_rand($filieres)] : null,
                'is_active' => true,
            ]
        );
        if ($etudiant1->wasRecentlyCreated) {
            $etudiant1->assignRole('eleve');
        }
        

        $etudiant2 = User::firstOrCreate(
            ['email' => 'etudiant2@example.com'],
            [
                'name' => 'Leila El Mansouri',
                'nom' => 'El Mansouri',
                'prenom' => 'Leila',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ELEVE->value,
                'phone' => $generatePhone(),
                'address' => '456 Avenue des Étudiantes, Rabat 10000',
                'date_naissance' => now()->subYears(rand(18, 22))->format('Y-m-d'),
                'niveau_id' => !empty($niveaux) ? $niveaux[array_rand($niveaux)] : null,
                'filiere_id' => !empty($filieres) ? $filieres[array_rand($filieres)] : null,
                'is_active' => true,
            ]
        );
        if ($etudiant2->wasRecentlyCreated) {
            $etudiant2->assignRole('eleve');
        }

        // Création des assistants (2)
        $assistant1 = User::firstOrCreate(
            ['email' => 'assistant1@example.com'],
            [
                'name' => 'Karim Bennis',
                'nom' => 'Bennis',
                'prenom' => 'Karim',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ASSISTANT->value,
                'phone' => $generatePhone(),
                'address' => '789 Rue des Assistants, Casablanca 20000',
                'is_active' => true,
            ]
        );
        if ($assistant1->wasRecentlyCreated) {
            $assistant1->assignRole('assistant');
        }

        $assistant2 = User::firstOrCreate(
            ['email' => 'assistant2@example.com'],
            [
                'name' => 'Salma El Amrani',
                'nom' => 'El Amrani',
                'prenom' => 'Salma',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => RoleType::ASSISTANT->value,
                'phone' => $generatePhone(),
                'address' => '101 Avenue des Assistantes, Rabat 10000',
                'is_active' => true,
            ]
        );
        if ($assistant2->wasRecentlyCreated) {
            $assistant2->assignRole('assistant');
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
