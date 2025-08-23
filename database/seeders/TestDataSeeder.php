<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Paiement;
use App\Models\Absence;
use App\Models\Matiere;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create a test student if not exists
        $student = User::firstOrCreate(
            ['email' => 'test.student@example.com'],
            [
                'name' => 'Test Student',
                'password' => bcrypt('password'),
                'role' => 4, // eleve
                'is_active' => true
            ]
        );

        // Create a test matiere if not exists
        $matiere = Matiere::firstOrCreate(
            ['code' => 'MATH'],
            [
                'nom' => 'Mathématiques',
                'description' => 'Cours de mathématiques',
                'coefficient' => 4,
                'nombre_heures' => 6,
                'prix_mensuel' => 250,
                'commission_prof' => 30
            ]
        );

        // Create test payments
        for ($i = 0; $i < 5; $i++) {
            Paiement::create([
                'etudiant_id' => $student->id,
                'matiere_id' => $matiere->id,
                'montant' => rand(500, 2000),
                'mode_paiement' => ['especes', 'cheque', 'virement', 'carte'][rand(0, 3)],
                'date_paiement' => now()->subDays(rand(1, 30)),
                'statut' => 'valide',
                'commentaires' => 'Paiement de test',
                'mois_periode' => now()->format('Y-m')
            ]);
        }

        // Create test absences
        for ($i = 0; $i < 5; $i++) {
            Absence::create([
                'etudiant_id' => $student->id,
                'matiere_id' => $matiere->id,
                'date_absence' => now()->subDays(rand(1, 30)),
                'type' => ['absence', 'retard'][rand(0, 1)],
                'duree_retard' => rand(5, 60),
                'justifiee' => (bool)rand(0, 1),
                'justification' => 'Test absence',
            ]);
        }

        $this->command->info('Test data created successfully!');
    }
}
