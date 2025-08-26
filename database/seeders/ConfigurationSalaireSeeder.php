<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConfigurationSalaire;
use App\Models\Matiere;
use Illuminate\Support\Facades\DB;

class ConfigurationSalaireSeeder extends Seeder
{
    /**
     * Exécute le seeder pour créer des configurations de salaire par défaut.
     */
    public function run(): void
    {
        // Désactiver la vérification des clés étrangères temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ConfigurationSalaire::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Récupérer toutes les matières
        $matieres = Matiere::all();
        
        if ($matieres->isEmpty()) {
            $this->command->info('Aucune matière trouvée. Veuillez d\'abord créer des matières.');
            return;
        }

        // Prix unitaires et commissions par défaut par type de matière
        $configurationsParType = [
            'Scientifique' => [
                'prix_unitaire' => 150,
                'commission' => 35,
                'prix_min' => 120,
                'prix_max' => 200,
            ],
            'Littéraire' => [
                'prix_unitaire' => 130,
                'commission' => 30,
                'prix_min' => 100,
                'prix_max' => 180,
            ],
            'Langue' => [
                'prix_unitaire' => 160,
                'commission' => 40,
                'prix_min' => 130,
                'prix_max' => 220,
            ],
            'Autre' => [
                'prix_unitaire' => 120,
                'commission' => 30,
                'prix_min' => 100,
                'prix_max' => 180,
            ],
        ];

        $configurations = [];
        
        foreach ($matieres as $matiere) {
            // Déterminer le type de configuration à utiliser
            $type = $this->determinerTypeMatiere($matiere->nom);
            $config = $configurationsParType[$type] ?? $configurationsParType['Autre'];
            
            $configurations[] = [
                'matiere_id' => $matiere->id,
                'prix_unitaire' => $config['prix_unitaire'],
                'commission_prof' => $config['commission'],
                'prix_min' => $config['prix_min'],
                'prix_max' => $config['prix_max'],
                'est_actif' => true,
                'description' => 'Configuration par défaut pour la matière ' . $matiere->nom,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insérer les configurations par lots
        foreach (array_chunk($configurations, 50) as $lot) {
            ConfigurationSalaire::insert($lot);
        }

        $this->command->info(sprintf('Création de %d configurations de salaire terminée.', count($configurations)));
    }

    /**
     * Détermine le type de matière en fonction de son nom
     */
    private function determinerTypeMatiere(string $nomMatiere): string
    {
        $nomMatiere = strtolower($nomMatiere);
        
        $typesScientifiques = ['math', 'physique', 'chimie', 'sciences', 'biologie', 'svt', 'technologie'];
        $typesLitteraires = ['français', 'philosophie', 'histoire', 'géographie', 'lettres', 'littérature'];
        $typesLangues = ['anglais', 'espagnol', 'allemand', 'arabe', 'italien', 'langue'];
        
        foreach ($typesScientifiques as $type) {
            if (str_contains($nomMatiere, $type)) {
                return 'Scientifique';
            }
        }
        
        foreach ($typesLitteraires as $type) {
            if (str_contains($nomMatiere, $type)) {
                return 'Littéraire';
            }
        }
        
        foreach ($typesLangues as $type) {
            if (str_contains($nomMatiere, $type)) {
                return 'Langue';
            }
        }
        
        return 'Autre';
    }
}
