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

        // Récupérer les niveaux et filières
        $niveaux = Niveau::all();
        $filieres = Filiere::all();
        $professeurs = User::whereHas('roles', function($q) {
            $q->where('name', 'professeur');
        })->get();

        if ($niveaux->isEmpty() || $filieres->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter InitialDataSeeder pour créer les niveaux et filières.');
            return;
        }

        // Créer 5 classes
        $anneeScolaire = (date('m') >= 9 ? date('Y') : date('Y') - 1) . '-' . (date('m') >= 9 ? date('Y') + 1 : date('Y'));
        
        // Tableau des classes à créer
        $classes = [
            ['libelle' => '1ère Année Bac Sciences Mathématiques', 'niveau_id' => $niveaux->where('libelle', '1ère année Bac Sciences')->first()->id, 'filiere_id' => $filieres->where('libelle', 'Sciences Mathématiques')->first()->id],
            ['libelle' => '2ème Année Bac Sciences Mathématiques', 'niveau_id' => $niveaux->where('libelle', '2ème année Bac Sciences')->first()->id, 'filiere_id' => $filieres->where('libelle', 'Sciences Mathématiques')->first()->id],
            ['libelle' => '1ère Année Bac Sciences Expérimentales', 'niveau_id' => $niveaux->where('libelle', '1ère année Bac Sciences')->first()->id, 'filiere_id' => $filieres->where('libelle', 'Sciences Expérimentales')->first()->id],
            ['libelle' => '2ème Année Bac Sciences Physiques', 'niveau_id' => $niveaux->where('libelle', '2ème année Bac Sciences')->first()->id, 'filiere_id' => $filieres->where('libelle', 'Sciences Physiques')->first()->id],
            ['libelle' => '2ème Année Bac Sciences Économiques', 'niveau_id' => $niveaux->where('libelle', '2ème année Bac Sciences')->first()->id, 'filiere_id' => $filieres->where('libelle', 'Sciences Économiques')->first()->id],
        ];

        foreach ($classes as $index => $classeData) {
            $professeurPrincipal = $professeurs->isNotEmpty() ? $professeurs->random() : null;
            
            Classe::create([
                'libelle' => $classeData['libelle'],
                'niveau_id' => $classeData['niveau_id'],
                'filiere_id' => $classeData['filiere_id'],
                'professeur_principal_id' => $professeurPrincipal ? $professeurPrincipal->id : null,
                'annee_scolaire' => $anneeScolaire,
                'effectif_max' => 30,
                'salle' => 'Salle ' . ($index + 1),
                'est_actif' => true,
            ]);
            
            $this->command->info("Classe créée : " . $classeData['libelle']);
        }
    }
}
