<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Niveau;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NiveauxFilieresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des niveaux
        $niveaux = [
            ['nom' => '6ème', 'code' => '6EME', 'ordre' => 1],
            ['nom' => '5ème', 'code' => '5EME', 'ordre' => 2],
            ['nom' => '4ème', 'code' => '4EME', 'ordre' => 3],
            ['nom' => '3ème', 'code' => '3EME', 'ordre' => 4],
            ['nom' => '2ème année bac', 'code' => '2BAC', 'ordre' => 5],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::firstOrCreate(
                ['code' => $niveau['code']],
                [
                    'nom' => $niveau['nom'],
                    'ordre' => $niveau['ordre'],
                    'est_actif' => true
                ]
            );
        }

        // Création des filières pour le bac
        $filieres = [
            ['niveau_id' => 5, 'nom' => 'Sciences Physiques', 'code' => '2BAC-SP', 'duree_annees' => 1, 'frais_mensuel' => 500],
            ['niveau_id' => 5, 'nom' => 'Sciences Mathématiques', 'code' => '2BAC-SM', 'duree_annees' => 1, 'frais_mensuel' => 500],
            ['niveau_id' => 5, 'nom' => 'Sciences Economiques', 'code' => '2BAC-SE', 'duree_annees' => 1, 'frais_mensuel' => 450],
            ['niveau_id' => 5, 'nom' => 'Sciences Expérimentales', 'code' => '2BAC-SV', 'duree_annees' => 1, 'frais_mensuel' => 480],
        ];

        foreach ($filieres as $filiere) {
            Filiere::firstOrCreate(
                ['code' => $filiere['code']],
                [
                    'niveau_id' => $filiere['niveau_id'],
                    'nom' => $filiere['nom'],
                    'duree_annees' => $filiere['duree_annees'],
                    'frais_mensuel' => $filiere['frais_mensuel'],
                    'frais_inscription' => 1000,
                    'est_actif' => true
                ]
            );
        }
    }
}
