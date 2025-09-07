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
        $this->command->info('Début du seeder CoursSeeder...');
        
        // Vérifier le nombre de cours avant la suppression
        $countBefore = DB::table('cours')->count();
        $this->command->info("1. Nombre de cours avant suppression: $countBefore");
        
        // Désactiver temporairement les contraintes de clé étrangère
        $this->command->info('2. Désactivation des contraintes de clé étrangère...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vérifier les contraintes de clé étrangère
        $foreignKeyStatus = DB::select("SELECT @@FOREIGN_KEY_CHECKS as status")[0]->status;
        $this->command->info("   - FOREIGN_KEY_CHECKS = $foreignKeyStatus");
        
        // Vider la table des cours
        $this->command->info('3. Vidage de la table cours...');
        
        // Afficher les informations sur la table avant suppression
        $countBefore = DB::table('cours')->count();
        $this->command->info(sprintf(
            '   - Avant suppression: %d enregistrements',
            $countBefore
        ));
        
        // Utiliser DELETE au lieu de TRUNCATE
        if ($countBefore > 0) {
            $deleted = DB::table('cours')->delete();
            $this->command->info(sprintf('   - %d enregistrements supprimés', $deleted));
            
            // Réinitialiser l'auto-incrément
            DB::statement('ALTER TABLE cours AUTO_INCREMENT = 1');
        }
        
        // Vérifier que la table est vide
        $countAfterDelete = DB::table('cours')->count();
        $this->command->info(sprintf(
            '4. Après suppression: %d enregistrements',
            $countAfterDelete
        ));
        
        // Réactiver les contraintes de clé étrangère
        $this->command->info('5. Réactivation des contraintes de clé étrangère...');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $foreignKeyStatus = DB::select("SELECT @@FOREIGN_KEY_CHECKS as status")[0]->status;
        $this->command->info("   - FOREIGN_KEY_CHECKS = $foreignKeyStatus");

        // Récupérer les classes, matières et professeurs
        $this->command->info('6. Récupération des classes, matières et professeurs...');
        $classes = Classe::with('niveau', 'filiere')->get();
        $matieres = Matiere::all();
        $professeurs = User::where('role', 2)->get(); // 2 = PROFESSEUR dans RoleType
        
        $this->command->info(sprintf(
            '7. Données récupérées: %d classes, %d matières, %d professeurs',
            $classes->count(),
            $matieres->count(),
            $professeurs->count()
        ));

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
                    $date = now()->addDays(rand(1, 30));
                    $heureDebut = now()->setTime(rand(8, 16), 0, 0);
                    $heureFin = (clone $heureDebut)->addHours(1);
                    
                    try {
                        $cours = new Cours([
                            'classe_id' => $classe->id,
                            'matiere_id' => $matiere->id,
                            'enseignant_id' => $professeur->id,
                            'date' => $date->format('Y-m-d'),
                            'heure_debut' => $heureDebut->format('H:i:s'),
                            'heure_fin' => $heureFin->format('H:i:s'),
                            'salle' => 'Salle ' . rand(1, 20),
                            'statut' => 'planifie',
                            'contenu' => 'Contenu du cours de ' . $matiere->nom . ' pour la classe ' . $classe->nom,
                            'devoirs' => 'Devoirs à faire pour le prochain cours',
                            'notes' => 'Notes importantes pour ce cours',
                            'est_valide' => rand(0, 1),
                            'valide_par' => $professeur->id,
                            'valide_le' => now()->format('Y-m-d H:i:s')
                        ]);
                        
                        $saved = $cours->save();
                        if (!$saved) {
                            $this->command->error("Échec de l'enregistrement du cours: " . json_encode($cours->toArray()));
                        } else {
                            $this->command->info("Cours enregistré avec l'ID: " . $cours->id);
                        }
                    } catch (\Exception $e) {
                        $this->command->error("Erreur lors de la création du cours: " . $e->getMessage());
                        $this->command->error("Détails: " . json_encode([
                            'classe_id' => $classe->id,
                            'matiere_id' => $matiere->id,
                            'enseignant_id' => $professeur->id,
                            'date' => $date->format('Y-m-d'),
                            'heure_debut' => $heureDebut->format('H:i:s'),
                            'heure_fin' => $heureFin->format('H:i:s')
                        ]));
                    }
                    
                    $this->command->info(sprintf(
                        "Création d'un cours de %s (ID: %d) pour la classe %s (ID: %d) avec le professeur %s (ID: %d)",
                        $matiere->nom ?? 'Inconnu',
                        $matiere->id,
                        $classe->nom ?? 'Inconnu',
                        $classe->id,
                        $professeur->name,
                        $professeur->id
                    ));
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
