<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = [
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH',
                'prix' => 300.00,
                'prix_prof' => 200.00,
                'type' => 'Scientifique',
                'description' => 'Cours de mathématiques couvrant divers sujets avancés',
            ],
            [
                'nom' => 'Sciences de la Vie et de la Terre (SVT)',
                'code' => 'SVT',
                'prix' => 250.00,
                'prix_prof' => 150.00,
                'type' => 'Scientifique',
                'description' => 'Étude des sciences de la vie et de la Terre',
            ],
            [
                'nom' => 'Physique-Chimie',
                'code' => 'PHY-CHI',
                'prix' => 280.00,
                'prix_prof' => 180.00,
                'type' => 'Scientifique',
                'description' => 'Cours de physique et chimie',
            ],
            [
                'nom' => 'Communication Française',
                'code' => 'FRAN',
                'prix' => 200.00,
                'prix_prof' => 100.00,
                'type' => 'Littéraire',
                'description' => 'Amélioration des compétences en communication en français',
            ],
            [
                'nom' => 'Communication Anglaise',
                'code' => 'ANGL',
                'prix' => 200.00,
                'prix_prof' => 100.00,
                'type' => 'Linguistique',
                'description' => 'Développement des compétences en communication en anglais',
            ],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(
                ['nom' => $matiere['nom']],
                $matiere
            );
        }
    }
}
