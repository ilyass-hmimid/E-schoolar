<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnseignementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $anneeScolaire = "$currentYear-$nextYear";
        
        // Get sample data
        $matieres = DB::table('matieres')->get()->keyBy('nom');
        $professeurs = DB::table('users')->where('role', 'professeur')->get();
        $classes = DB::table('classes')->get();
        
        // If no classes exist, we need to create some first
        if ($classes->isEmpty()) {
            $this->createSampleClasses();
            $classes = DB::table('classes')->get();
        }
        
        $enseignements = [];
        
        // Assign each matiere to a professor and a class
        foreach ($matieres as $matiere) {
            // Skip if no professors or classes available
            if ($professeurs->isEmpty() || $classes->isEmpty()) break;
            
            // Select random professor and class
            $professeur = $professeurs->random();
            $classe = $classes->random();
            
            // Determine number of hours based on subject type
            $nbHeures = $this->getNbHeuresForMatiere($matiere->nom);
            
            $enseignements[] = [
                'matiere_id' => $matiere->id,
                'professeur_id' => $professeur->id,
                'classe_id' => $classe->id,
                'nb_heures' => $nbHeures,
                'annee_scolaire' => $anneeScolaire,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Insert enseignements
        foreach ($enseignements as $enseignement) {
            // Check if this enseignement already exists
            $exists = DB::table('enseignements')
                ->where('matiere_id', $enseignement['matiere_id'])
                ->where('professeur_id', $enseignement['professeur_id'])
                ->where('classe_id', $enseignement['classe_id'])
                ->where('annee_scolaire', $enseignement['annee_scolaire'])
                ->exists();
                
            if (!$exists) {
                DB::table('enseignements')->insert($enseignement);
            }
        }
    }
    
    /**
     * Create sample classes if none exist
     */
    private function createSampleClasses(): void
    {
        $filieres = DB::table('filieres')->get();
        $niveaux = DB::table('niveaux')->get();
        $now = Carbon::now();
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $anneeScolaire = "$currentYear-$nextYear";
        
        $classes = [];
        
        // Create 2 classes per filiere
        foreach ($filieres as $filiere) {
            $niveau = $niveaux->firstWhere('id', $filiere->niveau_id);
            
            if (!$niveau) continue;
            
            // Create 2 sections (A and B) for each filiere
            for ($i = 1; $i <= 2; $i++) {
                $section = chr(64 + $i); // A, B, etc.
                $code = $filiere->code . $section;
                
                $classes[] = [
                    'filiere_id' => $filiere->id,
                    'niveau_id' => $filiere->niveau_id,
                    'nom' => "{$filiere->nom} - Section $section",
                    'code' => $code,
                    'description' => "Classe de {$filiere->nom} - Section $section",
                    'capacite_max' => 30,
                    'est_actif' => true,
                    'annee_scolaire' => $anneeScolaire,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        // Insert classes
        foreach ($classes as $classe) {
            if (!DB::table('classes')->where('code', $classe['code'])->where('annee_scolaire', $anneeScolaire)->exists()) {
                DB::table('classes')->insert($classe);
            }
        }
    }
    
    /**
     * Determine number of hours for a matiere based on its name
     */
    private function getNbHeuresForMatiere(string $matiereNom): int
    {
        // Core subjects typically have more hours
        $coreSubjects = ['Mathématiques', 'Physique', 'SVT', 'Français', 'Arabe', 'Anglais'];
        $isCore = false;
        
        foreach ($coreSubjects as $subject) {
            if (stripos($matiereNom, $subject) !== false) {
                $isCore = true;
                break;
            }
        }
        
        // Return 4 hours for core subjects, 2 for others
        return $isCore ? 4 : 2;
    }
}
