<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des classes
        DB::table('classes')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Récupérer les niveaux, filières et professeurs
        $niveaux = Niveau::all();
        $filieres = Filiere::all();
        $professeurs = User::where('role', 'professeur')->get();

        if ($niveaux->isEmpty() || $filieres->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter InitialDataSeeder pour créer les niveaux et filières.');
            return;
        }

        // Créer 5 classes
        $anneeScolaire = (date('m') >= 9 ? date('Y') : date('Y') - 1) . '-' . (date('m') >= 9 ? date('Y') + 1 : date('Y'));
        
        // Tableau des classes à créer
        $classes = [
            ['nom' => '1ère année Bac - Sciences Mathématiques', 'niveau_code' => '1BAC', 'filiere_code' => 'SM'],
            ['nom' => '2ème année Bac - Sciences Mathématiques', 'niveau_code' => '2BAC', 'filiere_code' => 'SM'],
            ['nom' => '1ère année Bac - Sciences Physiques', 'niveau_code' => '1BAC', 'filiere_code' => 'SPC'],
            ['nom' => '2ème année Bac - Sciences Physiques', 'niveau_code' => '2BAC', 'filiere_code' => 'SPC'],
            ['nom' => '2ème année Bac - Sciences Économiques', 'niveau_code' => '2BAC', 'filiere_code' => 'SE'],
        ];
        
        // Convertir les codes en IDs
        $classes = array_map(function($classe) use ($niveaux, $filieres) {
            $niveau = $niveaux->where('code', $classe['niveau_code'])->first();
            $filiere = $filieres->where('code', $classe['filiere_code'])->first();
            
            if (!$niveau || !$filiere) {
                $this->command->warn("Skipping class {$classe['nom']} - Niveau or Filiere not found");
                return null;
            }
            
            return [
                'nom' => $classe['nom'],
                'niveau_id' => $niveau->id,
                'filiere_id' => $filiere->id,
                'code_classe' => strtoupper(substr($classe['nom'], 0, 3)) . '_' . $niveau->code . '_' . $filiere->code,
            ];
        }, $classes);
        
        // Filtrer les classes valides
        $classes = array_filter($classes);

        foreach ($classes as $index => $classeData) {
            $professeurPrincipal = $professeurs->isNotEmpty() ? $professeurs->random() : null;
            
            Classe::create([
                'nom' => $classeData['nom'],
                'code' => $classeData['code_classe'],
                'niveau_id' => $classeData['niveau_id'],
                'filiere_id' => $classeData['filiere_id'],
                'description' => $classeData['nom'],
                'capacite_max' => 30,
                'est_actif' => true,
                'annee_scolaire' => $anneeScolaire,
            ]);
            
            $this->command->info("Classe créée : " . $classeData['nom']);
        }
    }
}
