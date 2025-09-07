<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Vider les tables si nécessaire (en développement uniquement)
        if (app()->environment('local', 'testing')) {
            Schema::disableForeignKeyConstraints();
            
            $tables = [
                'eleve_matiere',
                'professeur_matiere',
                'paiements',
                'absences',
                'salaires',
                'matieres',
                'users',
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            Schema::enableForeignKeyConstraints();
        }

        // Exécuter les seeders des données de base
        $this->call([
            MatiereSeeder::class,      // Les 5 matières fixes
            AdminUserSeeder::class,    // Compte administrateur
            TestUsersSeeder::class,    // Comptes de test (professeur et élève)
        ]);

        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Base de données initialisée avec succès!');
    }
    private function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
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
