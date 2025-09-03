<?php

namespace Database\Seeders;

use App\Models\Cours;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des cours
        DB::table('cours')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Récupérer les classes, matières et professeurs
        $classes = Classe::with('niveau', 'filiere')->get();
        $matieres = Matiere::all();
        $professeurs = User::whereHas('roles', function($q) {
            $q->where('name', 'professeur');
        })->get();

        if ($classes->isEmpty() || $matieres->isEmpty() || $professeurs->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter les seeders pour les classes, matières et professeurs.');
            return;
        }

        // Jours de la semaine
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        
        // Création des cours pour chaque classe
        foreach ($classes as $classe) {
            // Sélectionner 5 à 7 matières pour cette classe
            $matieresClasse = $matieres->random(rand(5, min(7, $matieres->count())));
            
            // Créer 2 à 3 cours par matière et par semaine
            foreach ($matieresClasse as $matiere) {
                // Sélectionner un professeur pour cette matière
                $professeur = $professeurs->random();
                
                // Créer 2 à 3 créneaux par semaine pour cette matière
                $nombreCours = rand(2, 3);
                $joursCours = array_rand(array_flip($jours), $nombreCours);
                
                foreach ($joursCours as $jour) {
                    // Heure de début aléatoire entre 8h et 18h
                    $heureDebut = rand(8, 17);
                    $heureFin = $heureDebut + 1; // Cours d'une heure
                    
                    // Créer le cours
                    Cours::create([
                        'niveau_id' => $classe->niveau_id,
                        'matiere_id' => $matiere->id,
                        'professeur_id' => $professeur->id,
                        'titre' => $matiere->libelle . ' - ' . $classe->nom,
                        'description' => 'Cours de ' . $matiere->libelle . ' pour la classe ' . $classe->nom,
                        'duree' => 60, // Durée en minutes
                        'prix' => rand(50, 200), // Prix aléatoire entre 50 et 200
                        'est_actif' => true
                    ]);
                    
                    $this->command->info("Création d'un cours de {$matiere->libelle} pour la classe {$classe->nom} avec le professeur {$professeur->name}");
                }
            }
        }
        
        $this->command->info('Cours créés avec succès !');
    }
    
    /**
     * Générer une couleur aléatoire au format hexadécimal
     */
    private function getRandomColor(): string
    {
        $colors = [
            '#3498db', // Bleu
            '#2ecc71', // Vert
            '#e74c3c', // Rouge
            '#f39c12', // Orange
            '#9b59b6', // Violet
            '#1abc9c', // Turquoise
            '#34495e', // Bleu foncé
            '#e67e22', // Orange foncé
            '#27ae60', // Vert foncé
            '#8e44ad', // Violet foncé
        ];
        
        return $colors[array_rand($colors)];
    }
}
