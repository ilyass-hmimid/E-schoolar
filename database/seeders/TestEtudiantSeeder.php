<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Etudiant;
use App\Models\Classe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestEtudiantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Get a class to assign students to
        $classe = Classe::first();
        
        if (!$classe) {
            $this->command->error('No classes found. Please run ClasseSeeder first.');
            return;
        }
        
        $etudiants = [
            [
                'name' => 'Mohamed Amine',
                'email' => 'mohamed.amine@example.com',
                'password' => Hash::make('password'),
                'role' => 'etudiant',
                'phone' => '0612345678',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'etudiant' => [
                    'code_etudiant' => 'ET' . rand(1000, 9999),
                    'nom' => 'Amine',
                    'prenom' => 'Mohamed',
                    'email' => 'mohamed.amine@example.com',
                    'telephone' => '0612345678',
                    'adresse' => '123 Rue des Étudiants',
                    'ville' => 'Casablanca',
                    'pays' => 'Maroc',
                    'date_naissance' => '2000-01-15',
                    'lieu_naissance' => 'Casablanca',
                    'cin' => 'A' . rand(100000, 999999),
                    'cne' => 'K' . rand(100000, 999999),
                    'sexe' => 'M',
                    'classe_id' => $classe->id,
                ]
            ],
            [
                'name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@example.com',
                'password' => Hash::make('password'),
                'role' => 'etudiant',
                'phone' => '0623456789',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'etudiant' => [
                    'code_etudiant' => 'ET' . rand(1000, 9999),
                    'nom' => 'Zahra',
                    'prenom' => 'Fatima',
                    'email' => 'fatima.zahra@example.com',
                    'telephone' => '0623456789',
                    'adresse' => '456 Avenue des Études',
                    'ville' => 'Rabat',
                    'pays' => 'Maroc',
                    'date_naissance' => '2001-03-22',
                    'lieu_naissance' => 'Rabat',
                    'cin' => 'B' . rand(100000, 999999),
                    'cne' => 'K' . rand(100000, 999999),
                    'sexe' => 'F',
                    'classe_id' => $classe->id,
                ]
            ],
            [
                'name' => 'Youssef Alaoui',
                'email' => 'youssef.alaoui@example.com',
                'password' => Hash::make('password'),
                'role' => 'etudiant',
                'phone' => '0634567890',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'etudiant' => [
                    'code_etudiant' => 'ET' . rand(1000, 9999),
                    'nom' => 'Alaoui',
                    'prenom' => 'Youssef',
                    'email' => 'youssef.alaoui@example.com',
                    'telephone' => '0634567890',
                    'adresse' => '789 Boulevard des Étudiants',
                    'ville' => 'Marrakech',
                    'pays' => 'Maroc',
                    'date_naissance' => '1999-11-05',
                    'lieu_naissance' => 'Marrakech',
                    'cin' => 'C' . rand(100000, 999999),
                    'cne' => 'K' . rand(100000, 999999),
                    'sexe' => 'M',
                    'classe_id' => $classe->id,
                ]
            ],
            [
                'name' => 'Amina Benani',
                'email' => 'amina.benani@example.com',
                'password' => Hash::make('password'),
                'role' => 'etudiant',
                'phone' => '0645678901',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'etudiant' => [
                    'code_etudiant' => 'ET' . rand(1000, 9999),
                    'nom' => 'Benani',
                    'prenom' => 'Amina',
                    'email' => 'amina.benani@example.com',
                    'telephone' => '0645678901',
                    'adresse' => '321 Rue des Études',
                    'ville' => 'Fès',
                    'pays' => 'Maroc',
                    'date_naissance' => '2000-07-18',
                    'lieu_naissance' => 'Fès',
                    'cin' => 'D' . rand(100000, 999999),
                    'cne' => 'K' . rand(100000, 999999),
                    'sexe' => 'F',
                    'classe_id' => $classe->id,
                ]
            ],
            [
                'name' => 'Mehdi El Fassi',
                'email' => 'mehdi.elfassi@example.com',
                'password' => Hash::make('password'),
                'role' => 'etudiant',
                'phone' => '0656789012',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'etudiant' => [
                    'code_etudiant' => 'ET' . rand(1000, 9999),
                    'nom' => 'El Fassi',
                    'prenom' => 'Mehdi',
                    'email' => 'mehdi.elfassi@example.com',
                    'telephone' => '0656789012',
                    'adresse' => '202 Rue des Études',
                    'ville' => 'Tanger',
                    'pays' => 'Maroc',
                    'date_naissance' => '2001-02-28',
                    'lieu_naissance' => 'Tanger',
                    'cin' => 'E' . rand(100000, 999999),
                    'cne' => 'K' . rand(100000, 999999),
                    'sexe' => 'M',
                    'classe_id' => $classe->id,
                ]
            ],
        ];

        // Create users and associated etudiants
        foreach ($etudiants as $etudiantData) {
            // Check if user already exists
            $user = User::where('email', $etudiantData['email'])->first();
            
            if (!$user) {
                // Create user
                $user = User::create([
                    'name' => $etudiantData['name'],
                    'email' => $etudiantData['email'],
                    'password' => $etudiantData['password'],
                    'role' => $etudiantData['role'],
                    'phone' => $etudiantData['phone'],
                    'is_active' => $etudiantData['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            
            // Create etudiant record if it doesn't exist
            if (!Etudiant::where('email', $etudiantData['etudiant']['email'])->exists()) {
                $etudiantData['etudiant']['user_id'] = $user->id;
                Etudiant::create($etudiantData['etudiant']);
            }
        }

        $this->command->info('Successfully seeded test students and their user accounts.');
    }
}
