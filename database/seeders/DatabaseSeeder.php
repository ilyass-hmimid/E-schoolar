<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Matiere;
use App\Models\Pack;
use App\Enums\RoleType;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Exécuter les seeders des rôles, permissions et configurations
        $this->call([
            RolePermissionSeeder::class,
            PermissionSeeder::class,
            ConfigurationSalaireSeeder::class,
            UserSeeder::class,  // Ajout du UserSeeder
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
            ]
        );

        // Attribuer le rôle admin à l'utilisateur admin
        $admin->assignRole('admin');

        // Seed des configurations de salaire
        $this->call([
            ConfigurationSalaireSeeder::class,
        ]);

        // Créer les niveaux
        $niveaux = [
            ['nom' => '3ème Année Collège', 'code' => '3AC', 'ordre' => 1],
            ['nom' => '2ème Année Collège', 'code' => '2AC', 'ordre' => 2],
            ['nom' => '1ère Année Collège', 'code' => '1AC', 'ordre' => 3],
            ['nom' => 'Tronc Commun', 'code' => 'TC', 'ordre' => 4],
            ['nom' => '1ère Année Bac', 'code' => '1BAC', 'ordre' => 5],
            ['nom' => '2ème Année Bac - Sciences Mathématiques', 'code' => '2BAC-MATH', 'ordre' => 6],
            ['nom' => '2ème Année Bac - Sciences Physiques', 'code' => '2BAC-PC', 'ordre' => 7],
            ['nom' => '2ème Année Bac - Sciences de la Vie et de la Terre', 'code' => '2BAC-SVT', 'ordre' => 8],
            ['nom' => '2ème Année Bac - Sciences Économiques', 'code' => '2BAC-ECO', 'ordre' => 9],
            ['nom' => '2ème Année Bac - Lettres', 'code' => '2BAC-LET', 'ordre' => 10],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::firstOrCreate(['code' => $niveau['code']], $niveau);
        }

        // Créer les filières
        $filieres = [
            ['nom' => 'Sciences Mathématiques', 'code' => 'SM', 'duree_annees' => 2, 'frais_inscription' => 500, 'frais_mensuel' => 300],
            ['nom' => 'Sciences Physiques', 'code' => 'SP', 'duree_annees' => 2, 'frais_inscription' => 500, 'frais_mensuel' => 300],
            ['nom' => 'Sciences de la Vie et de la Terre', 'code' => 'SVT', 'duree_annees' => 2, 'frais_inscription' => 500, 'frais_mensuel' => 300],
            ['nom' => 'Sciences Économiques', 'code' => 'SE', 'duree_annees' => 2, 'frais_inscription' => 400, 'frais_mensuel' => 250],
            ['nom' => 'Lettres', 'code' => 'LET', 'duree_annees' => 2, 'frais_inscription' => 400, 'frais_mensuel' => 250],
        ];

        foreach ($filieres as $filiere) {
            Filiere::firstOrCreate(['code' => $filiere['code']], $filiere);
        }

        // Créer les matières
        $matieres = [
            ['nom' => 'Mathématiques', 'code' => 'MATH', 'coefficient' => 4, 'nombre_heures' => 6, 'prix_mensuel' => 250, 'commission_prof' => 30],
            ['nom' => 'Physique-Chimie', 'code' => 'PC', 'coefficient' => 3, 'nombre_heures' => 4, 'prix_mensuel' => 200, 'commission_prof' => 30],
            ['nom' => 'Sciences de la Vie et de la Terre', 'code' => 'SVT', 'coefficient' => 2, 'nombre_heures' => 3, 'prix_mensuel' => 180, 'commission_prof' => 30],
            ['nom' => 'Français', 'code' => 'FR', 'coefficient' => 2, 'nombre_heures' => 3, 'prix_mensuel' => 150, 'commission_prof' => 25],
            ['nom' => 'Anglais', 'code' => 'EN', 'coefficient' => 1, 'nombre_heures' => 2, 'prix_mensuel' => 120, 'commission_prof' => 25],
            ['nom' => 'Histoire-Géographie', 'code' => 'HG', 'coefficient' => 2, 'nombre_heures' => 3, 'prix_mensuel' => 150, 'commission_prof' => 25],
            ['nom' => 'Philosophie', 'code' => 'PHILO', 'coefficient' => 2, 'nombre_heures' => 3, 'prix_mensuel' => 150, 'commission_prof' => 25],
            ['nom' => 'Économie', 'code' => 'ECO', 'coefficient' => 3, 'nombre_heures' => 4, 'prix_mensuel' => 200, 'commission_prof' => 30],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(['code' => $matiere['code']], $matiere);
        }

        // Créer les packs
        $packs = [
            ['nom' => 'Pack Mathématiques', 'description' => 'Cours de mathématiques intensifs', 'prix' => 500, 'duree_jours' => 30],
            ['nom' => 'Pack Sciences', 'description' => 'Mathématiques + Physique-Chimie', 'prix' => 700, 'duree_jours' => 60],
            ['nom' => 'Pack Complet', 'description' => 'Toutes les matières principales', 'prix' => 1200, 'duree_jours' => 90],
            ['nom' => 'Pack Soutien', 'description' => 'Soutien scolaire personnalisé', 'prix' => 400, 'duree_jours' => 30],
        ];

        foreach ($packs as $pack) {
            Pack::firstOrCreate(['nom' => $pack['nom']], $pack);
        }

        // Créer quelques utilisateurs de test
        User::firstOrCreate(
            ['email' => 'prof.math@allotawjih.com'],
            [
                'name' => 'Professeur Mathématiques',
                'password' => Hash::make('password'),
                'role' => RoleType::PROFESSEUR,
                'phone' => '0612345679',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'assistant@allotawjih.com'],
            [
                'name' => 'Assistant Réception',
                'password' => Hash::make('password'),
                'role' => RoleType::ASSISTANT,
                'phone' => '0612345680',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'eleve@allotawjih.com'],
            [
                'name' => 'Élève Test',
                'password' => Hash::make('password'),
                'role' => RoleType::ELEVE,
                'phone' => '0612345681',
                'niveau_id' => 6, // 2ème BAC Math
                'filiere_id' => 1, // Sciences Mathématiques
                'somme_a_payer' => 500,
                'date_debut' => now(),
                'is_active' => true,
            ]
        );

        $this->command->info('Données de base créées avec succès !');
        $this->command->info('Compte admin: admin@allotawjih.com / password');
        $this->command->info('Compte prof: prof.math@allotawjih.com / password');
        $this->command->info('Compte assistant: assistant@allotawjih.com / password');
        $this->command->info('Compte élève: eleve@allotawjih.com / password');
    }
}
