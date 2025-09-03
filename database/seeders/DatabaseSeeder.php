<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Vider les tables si nécessaire (en développement uniquement)
        if (app()->environment('local', 'testing')) {
            $this->truncateTables([
                'users', 'etudiants', 'enseignants', 'classes', 'cours',
                'paiements', 'absences', 'notifications', 'niveaux',
                'filieres', 'matieres', 'model_has_roles', 'roles', 'permissions',
                'model_has_permissions', 'role_has_permissions', 'enseignements', 'inscriptions'
            ]);
        }

        // Exécuter les seeders des données de base
        $this->call([
            RolePermissionSeeder::class,
            PermissionSeeder::class,
            ConfigurationSalaireSeeder::class,
            InitialDataSeeder::class, // Niveaux, filières et matières
            TestDataSeeder::class, // Données de test réalistes
        ]);

        // Créer l'administrateur principal
        $admin = User::firstOrCreate(
            ['email' => 'admin@allotawjih.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => RoleType::ADMIN,
                'phone' => '0612345678',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Attribuer le rôle admin à l'utilisateur admin
        $admin->assignRole('admin');

        // Créer des données de test pour le développement
        if (app()->environment('local', 'testing')) {
            $this->call([
                UserSeeder::class,
                ClasseSeeder::class,
                EtudiantSeeder::class,
                CoursSeeder::class,
                PaiementSeeder::class,
                AbsenceSeeder::class,
                NotificationSeeder::class,
            ]);
        } else {
            // En production, créer uniquement les comptes de base
            $this->createTestUsers();
        }

        // Réactiver les contraintes de clé étrangère
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Données de base créées avec succès !');
        $this->command->info('Compte admin: admin@allotawjih.com / password');
    }

    /**
     * Vider les tables spécifiées.
     *
     * @param array $tables
     * @return void
     */
    private function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            \DB::table($table)->truncate();
        }
    }

    /**
     * Créer des utilisateurs de test pour le développement
     */
    private function createTestUsers(): void
    {
        // Créer un professeur de test
        $professeur = User::firstOrCreate(
            ['email' => 'prof.math@allotawjih.com'],
            [
                'name' => 'Professeur Mathématiques',
                'password' => Hash::make('password'),
                'role' => RoleType::PROFESSEUR,
                'phone' => '0612345679',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $professeur->assignRole('professeur');

        // Créer un assistant de test
        $assistant = User::firstOrCreate(
            ['email' => 'assistant@allotawjih.com'],
            [
                'name' => 'Assistant Réception',
                'password' => Hash::make('password'),
                'role' => RoleType::ASSISTANT,
                'phone' => '0612345680',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $assistant->assignRole('assistant');

        // Créer un élève de test
        $eleve = User::firstOrCreate(
            ['email' => 'eleve@allotawjih.com'],
            [
                'name' => 'Élève Test',
                'password' => Hash::make('password'),
                'role' => RoleType::ELEVE,
                'phone' => '0612345681',
                'niveau_id' => 1, // Premier niveau disponible
                'filiere_id' => 1, // Première filière disponible
                'somme_a_payer' => 500,
                'date_debut' => now(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $eleve->assignRole('eleve');
    }
}
