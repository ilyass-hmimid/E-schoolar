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
                'prix_mensuel' => 300,
                'couleur' => '#3b82f6', // Bleu
            ],
            [
                'nom' => 'Sciences de la Vie et de la Terre (SVT)',
                'prix_mensuel' => 250,
                'couleur' => '#10b981', // Vert
            ],
            [
                'nom' => 'Physique-Chimie',
                'prix_mensuel' => 280,
                'couleur' => '#8b5cf6', // Violet
            ],
            [
                'nom' => 'Communication Française',
                'prix_mensuel' => 200,
                'couleur' => '#ec4899', // Rose
            ],
            [
                'nom' => 'Communication Anglaise',
                'prix_mensuel' => 200,
                'couleur' => '#f59e0b', // Jaune
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
