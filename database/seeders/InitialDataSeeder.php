<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Matiere;
use App\Models\Niveau;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Exécuter les seeds de la base de données.
     */
    public function run(): void
    {
        $this->createNiveaux();
        $this->createFilieres();
        $this->createMatieres();
    }

    /**
     * Créer les niveaux scolaires.
     */
    private function createNiveaux(): void
    {
        $niveaux = [
            [
                'code' => '6EME', 
                'nom' => '6ème', 
                'description' => 'Sixième année du collège',
                'ordre' => 1,
                'est_actif' => true
            ],
            [
                'code' => '5EME', 
                'nom' => '5ème', 
                'description' => 'Cinquième année du collège',
                'ordre' => 2,
                'est_actif' => true
            ],
            [
                'code' => '4EME', 
                'nom' => '4ème', 
                'description' => 'Quatrième année du collège',
                'ordre' => 3,
                'est_actif' => true
            ],
            [
                'code' => '3EME', 
                'nom' => '3ème', 
                'description' => 'Troisième année du collège',
                'ordre' => 4,
                'est_actif' => true
            ],
            [
                'code' => 'TC', 
                'nom' => 'Tronc Commun', 
                'description' => 'Tronc commun scientifique et littéraire',
                'ordre' => 5,
                'est_actif' => true
            ],
            [
                'code' => '1BAC', 
                'nom' => '1ère année Bac', 
                'description' => 'Première année du baccalauréat',
                'ordre' => 6,
                'est_actif' => true
            ],
            [
                'code' => '2BAC', 
                'nom' => '2ème année Bac', 
                'description' => 'Deuxième année du baccalauréat',
                'ordre' => 7,
                'est_actif' => true
            ]
        ];

        foreach ($niveaux as $niveau) {
            Niveau::firstOrCreate(['code' => $niveau['code']], $niveau);
        }
    }

    /**
     * Créer les filières d'étude.
     */
    private function createFilieres(): void
    {
        // Get the niveau for baccalaureate (1BAC)
        $niveauBac1 = Niveau::where('code', '1BAC')->first();
        $niveauBac2 = Niveau::where('code', '2BAC')->first();
        
        if (!$niveauBac1 || !$niveauBac2) {
            throw new \RuntimeException('Required niveaux (1BAC/2BAC) not found. Please run createNiveaux first.');
        }
        
        $filieres = [
            [
                'code' => 'SM', 
                'nom' => 'Sciences Mathématiques', 
                'description' => 'Filière Sciences Mathématiques',
                'niveau_id' => $niveauBac1->id,
                'duree_annees' => 2,
                'frais_inscription' => 1500,
                'frais_mensuel' => 800,
                'est_actif' => true
            ],
            [
                'code' => 'SPC', 
                'nom' => 'Sciences Physiques', 
                'description' => 'Filière Sciences Physiques et Chimie',
                'niveau_id' => $niveauBac1->id,
                'duree_annees' => 2,
                'frais_inscription' => 1400,
                'frais_mensuel' => 750,
                'est_actif' => true
            ],
            [
                'code' => 'SVT', 
                'nom' => 'Sciences Vie et Terre', 
                'description' => 'Filière Sciences de la Vie et de la Terre',
                'niveau_id' => $niveauBac1->id,
                'duree_annees' => 2,
                'frais_inscription' => 1300,
                'frais_mensuel' => 700,
                'est_actif' => true
            ],
            [
                'code' => 'LET', 
                'nom' => 'Lettres', 
                'description' => 'Filière Littéraire',
                'niveau_id' => $niveauBac1->id,
                'duree_annees' => 2,
                'frais_inscription' => 1200,
                'frais_mensuel' => 650,
                'est_actif' => true
            ],
            [
                'code' => 'ECO', 
                'nom' => 'Sciences Économiques', 
                'description' => 'Filière Sciences Économiques',
                'niveau_id' => $niveauBac1->id,
                'duree_annees' => 2,
                'frais_inscription' => 1400,
                'frais_mensuel' => 750,
                'est_actif' => true
            ],
            [
                'code' => 'SH', 
                'nom' => 'Sciences Humaines', 
                'description' => 'Filière Sciences Humaines',
                'niveau_id' => $niveauBac1->id,
                'duree_annees' => 2,
                'frais_inscription' => 1300,
                'frais_mensuel' => 700,
                'est_actif' => true
            ]
        ];

        foreach ($filieres as $filiere) {
            // Check if filiere exists by code
            $existingFiliere = Filiere::where('code', $filiere['code'])->first();
            
            if ($existingFiliere) {
                // Update existing filiere with the new data
                $existingFiliere->update($filiere);
            } else {
                // Create new filiere
                Filiere::create($filiere);
            }
        }
    }

    /**
     * Créer les matières scolaires.
     */
    private function createMatieres(): void
    {
        $matieres = [
            // Matières scientifiques
            [
                'code' => 'MATH', 
                'nom' => 'Mathématiques', 
                'description' => 'Mathématiques', 
                'type' => 'scientifique', 
                'prix' => 300, 
                'prix_prof' => 150
            ],
            [
                'code' => 'PHYS', 
                'nom' => 'Physique', 
                'description' => 'Physique', 
                'type' => 'scientifique', 
                'prix' => 280, 
                'prix_prof' => 140
            ],
            [
                'code' => 'CHIM', 
                'nom' => 'Chimie', 
                'description' => 'Chimie', 
                'type' => 'scientifique', 
                'prix' => 260, 
                'prix_prof' => 130
            ],
            [
                'code' => 'SVT', 
                'nom' => 'Sciences de la Vie et de la Terre', 
                'description' => 'SVT', 
                'type' => 'scientifique', 
                'prix' => 250, 
                'prix_prof' => 125
            ],
            
            // Matières littéraires
            [
                'code' => 'FR', 
                'nom' => 'Français', 
                'description' => 'Langue Française', 
                'type' => 'litteraire', 
                'prix' => 200, 
                'prix_prof' => 100
            ],
            [
                'code' => 'EN', 
                'nom' => 'Anglais', 
                'description' => 'Langue Anglaise', 
                'type' => 'litteraire', 
                'prix' => 180, 
                'prix_prof' => 90
            ],
            [
                'code' => 'AR', 
                'nom' => 'Arabe', 
                'description' => 'Langue Arabe', 
                'type' => 'litteraire', 
                'prix' => 180, 
                'prix_prof' => 90
            ],
            [
                'code' => 'PHILO', 
                'nom' => 'Philosophie', 
                'description' => 'Philosophie', 
                'type' => 'litteraire', 
                'prix' => 200, 
                'prix_prof' => 100
            ],
            [
                'code' => 'HISTGEO', 
                'nom' => 'Histoire-Géographie', 
                'description' => 'Histoire et Géographie', 
                'type' => 'litteraire', 
                'prix' => 180, 
                'prix_prof' => 90
            ],
            
            // Autres matières
            [
                'code' => 'ISLAM', 
                'nom' => 'Éducation Islamique', 
                'description' => 'Éducation Islamique', 
                'type' => 'autre', 
                'prix' => 150, 
                'prix_prof' => 75
            ],
            [
                'code' => 'EPS', 
                'nom' => 'Éducation Physique et Sportive', 
                'description' => 'EPS', 
                'type' => 'technique', 
                'prix' => 100, 
                'prix_prof' => 50
            ],
            [
                'code' => 'INFO', 
                'nom' => 'Informatique', 
                'description' => 'Informatique et Programmation', 
                'type' => 'scientifique', 
                'prix' => 250, 
                'prix_prof' => 125
            ],
            
            // Matières économiques
            [
                'code' => 'ECO', 
                'nom' => 'Sciences Économiques', 
                'description' => 'Économie Générale', 
                'type' => 'autre', 
                'prix' => 280, 
                'prix_prof' => 140
            ],
            [
                'code' => 'COMPTA', 
                'nom' => 'Comptabilité', 
                'description' => 'Comptabilité et Gestion', 
                'type' => 'autre', 
                'prix' => 260, 
                'prix_prof' => 130
            ],
            [
                'code' => 'STAT', 
                'nom' => 'Statistiques', 
                'description' => 'Statistiques et Probabilités', 
                'type' => 'scientifique', 
                'prix' => 240, 
                'prix_prof' => 120
            ],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(['code' => $matiere['code']], $matiere);
        }
    }
}
