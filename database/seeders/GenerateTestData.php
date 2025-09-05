<?php

namespace Database\Seeders;

use App\Models\Cours;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\User;
use App\Models\Absence;
use App\Models\Eleve;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenerateTestData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Vider les tables
        DB::table('absences')->truncate();
        DB::table('cours')->truncate();
        
        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Récupérer les données nécessaires
        $classes = Classe::all();
        $matieres = Matiere::all();
        $professeurs = User::role('professeur')->get();
        $eleves = Eleve::all();
        
        // Attribuer une classe à chaque élève qui n'en a pas
        foreach ($eleves as $eleve) {
            if (empty($eleve->classe_id)) {
                $eleve->classe_id = $classes->random()->id;
                $eleve->save();
            }
        }
        
        if ($classes->isEmpty() || $matieres->isEmpty() || $professeurs->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter les seeders pour les classes, matières et professeurs.');
            return;
        }
        
        // Générer des cours pour les 30 derniers jours
        $startDate = now()->subDays(30);
        $endDate = now();
        
        $this->command->info('Génération des cours...');
        
        $coursCrees = 0;
        
        foreach ($classes as $classe) {
            // Sélectionner 5-8 matières aléatoires pour cette classe
            $matieresClasse = $matieres->random(rand(5, 8));
            
            // Générer des cours pour chaque jour ouvré sur la période
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                // Ne générer des cours que du lundi au vendredi
                if ($date->isWeekend()) {
                    continue;
                }
                
                // Générer 4-6 cours par jour
                $nbCours = rand(4, 6);
                $heureDebut = 8; // 8h du matin
                
                for ($i = 0; $i < $nbCours; $i++) {
                    // Sélectionner une matière aléatoire pour ce créneau
                    $matiere = $matieresClasse->random();
                    $professeur = $professeurs->random();
                    
                    // Créer le cours
                    Cours::create([
                        'classe_id' => $classe->id,
                        'matiere_id' => $matiere->id,
                        'enseignant_id' => $professeur->id,
                        'date' => $date->format('Y-m-d'),
                        'heure_debut' => sprintf('%02d:00:00', $heureDebut),
                        'heure_fin' => sprintf('%02d:50:00', $heureDebut + 1), // Cours d'1h
                        'salle' => 'Salle ' . rand(1, 20),
                        'statut' => 'effectue',
                        'contenu' => 'Cours de ' . $matiere->nom,
                        'est_valide' => true,
                        'valide_le' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $coursCrees++;
                    $heureDebut += 2; // 1h de cours + 1h de pause
                }
            }
        }
        
        $this->command->info("$coursCrees cours générés avec succès !");
        
        // Maintenant, générer des absences pour ces cours
        $this->command->info('Génération des absences...');
        
        $absencesCreees = 0;
        $cours = Cours::all();
        
        foreach ($cours as $coursItem) {
            // Récupérer les élèves de la classe
            $eleves = Eleve::where('classe_id', $coursItem->classe_id)->get();
            
            if ($eleves->isEmpty()) {
                continue;
            }
            
            // Générer 0-3 absences aléatoires pour ce cours
            $nbAbsences = rand(0, 3);
            $elevesAbsents = $eleves->random(min($nbAbsences, $eleves->count()));
            
            foreach ($elevesAbsents as $eleve) {
                Absence::create([
                    'etudiant_id' => $eleve->user_id,
                    'matiere_id' => $coursItem->matiere_id,
                    'professeur_id' => $coursItem->enseignant_id,
                    'date_absence' => $coursItem->date,
                    'heure_debut' => $coursItem->heure_debut,
                    'heure_fin' => $coursItem->heure_fin,
                    'type' => 'absence',
                    'justifiee' => rand(0, 1) === 1, // 50% de chances d'être justifiée
                    'motif' => $this->getMotifAleatoire(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $absencesCreees++;
            }
        }
        
        $this->command->info("$absencesCreees absences générées avec succès !");
    }
    
    /**
     * Retourne un motif d'absence aléatoire
     */
    private function getMotifAleatoire(): string
    {
        $motifs = [
            'Maladie',
            'Rendez-vous médical',
            'Problème de transport',
            'Raison familiale',
            'Absence non justifiée',
            'Retard',
            'Sortie scolaire',
            'Compétition sportive',
        ];
        
        return $motifs[array_rand($motifs)];
    }
}
