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
            ['libelle' => '6ème', 'description' => 'Sixième année du collège'],
            ['libelle' => '5ème', 'description' => 'Cinquième année du collège'],
            ['libelle' => '4ème', 'description' => 'Quatrième année du collège'],
            ['libelle' => '3ème', 'description' => 'Troisième année du collège'],
            ['libelle' => '2ème année Bac', 'description' => 'Deuxième année du baccalauréat'],
            ['libelle' => '1ère année Bac', 'description' => 'Première année du baccalauréat'],
            ['libelle' => 'Tronc Commun', 'description' => 'Tronc commun scientifique et littéraire'],
            ['libelle' => '1ère année Bac Sciences', 'description' => 'Première année baccalauréat scientifique'],
            ['libelle' => '2ème année Bac Sciences', 'description' => 'Deuxième année baccalauréat scientifique'],
            ['libelle' => '1ère année Bac Lettres', 'description' => 'Première année baccalauréat littéraire'],
            ['libelle' => '2ème année Bac Lettres', 'description' => 'Deuxième année baccalauréat littéraire'],
            ['libelle' => '1ère année Bac Économie', 'description' => 'Première année baccalauréat sciences économiques'],
            ['libelle' => '2ème année Bac Économie', 'description' => 'Deuxième année baccalauréat sciences économiques'],
            ['libelle' => '1ère année Bac Techniques', 'description' => 'Première année baccalauréat techniques'],
            ['libelle' => '2ème année Bac Techniques', 'description' => 'Deuxième année baccalauréat techniques'],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::firstOrCreate(['libelle' => $niveau['libelle']], $niveau);
        }
    }

    /**
     * Créer les filières d'étude.
     */
    private function createFilieres(): void
    {
        $filieres = [
            ['libelle' => 'Sciences Mathématiques', 'description' => 'Filière Sciences Mathématiques'],
            ['libelle' => 'Sciences Expérimentales', 'description' => 'Filière Sciences Expérimentales'],
            ['libelle' => 'Sciences Économiques', 'description' => 'Filière Sciences Économiques'],
            ['libelle' => 'Lettres', 'description' => 'Filière Lettres'],
            ['libelle' => 'Sciences Humaines', 'description' => 'Filière Sciences Humaines'],
            ['libelle' => 'Sciences et Technologies Électriques', 'description' => 'Filière Sciences et Technologies Électriques'],
            ['libelle' => 'Sciences et Technologies Mécaniques', 'description' => 'Filière Sciences et Technologies Mécaniques'],
        ];

        foreach ($filieres as $filiere) {
            Filiere::firstOrCreate(['libelle' => $filiere['libelle']], $filiere);
        }
    }

    /**
     * Créer les matières scolaires.
     */
    private function createMatieres(): void
    {
        $matieres = [
            // Matières scientifiques
            ['libelle' => 'Mathématiques', 'description' => 'Mathématiques', 'coefficient' => 4],
            ['libelle' => 'Physique', 'description' => 'Physique', 'coefficient' => 3],
            ['libelle' => 'Chimie', 'description' => 'Chimie', 'coefficient' => 2],
            ['libelle' => 'Sciences de la Vie et de la Terre', 'description' => 'SVT', 'coefficient' => 2],
            
            // Matières littéraires
            ['libelle' => 'Français', 'description' => 'Langue Française', 'coefficient' => 3],
            ['libelle' => 'Anglais', 'description' => 'Langue Anglaise', 'coefficient' => 2],
            ['libelle' => 'Arabe', 'description' => 'Langue Arabe', 'coefficient' => 2],
            ['libelle' => 'Philosophie', 'description' => 'Philosophie', 'coefficient' => 2],
            ['libelle' => 'Histoire-Géographie', 'description' => 'Histoire et Géographie', 'coefficient' => 2],
            
            // Autres matières
            ['libelle' => 'Éducation Islamique', 'description' => 'Éducation Islamique', 'coefficient' => 1],
            ['libelle' => 'Éducation Physique et Sportive', 'description' => 'EPS', 'coefficient' => 1],
            ['libelle' => 'Informatique', 'description' => 'Informatique et Programmation', 'coefficient' => 2],
            
            // Matières économiques
            ['libelle' => 'Sciences Économiques', 'description' => 'Économie Générale', 'coefficient' => 3],
            ['libelle' => 'Comptabilité', 'description' => 'Comptabilité et Gestion', 'coefficient' => 3],
            ['libelle' => 'Statistiques', 'description' => 'Statistiques et Probabilités', 'coefficient' => 2],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(['libelle' => $matiere['libelle']], $matiere);
        }
    }
}
