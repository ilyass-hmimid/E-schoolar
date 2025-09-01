<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Cours;
use App\Enums\StatutAbsence;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des absences
        DB::table('absences')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Récupérer les étudiants et les cours
        $etudiants = Etudiant::with(['user', 'classe'])->get();
        $cours = Cours::all();
        
        if ($etudiants->isEmpty() || $cours->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter les seeders pour les étudiants et les cours.');
            return;
        }
        
        $absencesCreees = 0;
        $debutPeriode = now()->subMonths(3); // 3 derniers mois
        $finPeriode = now();
        
        // Pour chaque cours, générer des absences aléatoires
        foreach ($cours as $coursItem) {
            // Récupérer les étudiants de la classe du cours
            $etudiantsClasse = $etudiants->where('classe_id', $coursItem->classe_id);
            
            // Générer des dates de cours pour les 3 derniers mois
            $dateCours = Carbon::parse($coursItem->date_debut);
            
            while ($dateCours->lte($finPeriode)) {
                // Vérifier si c'est le bon jour de la semaine
                $jourSemaine = $this->getJourSemaine($coursItem->jour);
                
                if ($dateCours->dayOfWeek === $jourSemaine && $dateCours->between($debutPeriode, $finPeriode)) {
                    // Pour chaque étudiant, 10% de chance d'être absent
                    foreach ($etudiantsClasse as $etudiant) {
                        if (rand(1, 100) <= 10) { // 10% de chance d'être absent
                            // Statut aléatoire (70% non justifié, 20% justifié, 10% en attente)
                            $statut = StatutAbsence::NON_JUSTIFIEE;
                            $justification = null;
                            
                            $rand = rand(1, 100);
                            if ($rand <= 20) {
                                $statut = StatutPaiement::JUSTIFIEE;
                                $justification = $this->getRandomJustification();
                            } elseif ($rand <= 30) {
                                $statut = StatutPaiement::EN_ATTENTE;
                                $justification = 'En attente de justificatif';
                            }
                            
                            // Créer l'absence
                            Absence::create([
                                'etudiant_id' => $etudiant->id,
                                'cours_id' => $coursItem->id,
                                'date_absence' => $dateCours->toDateString(),
                                'heure_debut' => $coursItem->heure_debut,
                                'heure_fin' => $coursItem->heure_fin,
                                'statut' => $statut,
                                'justification' => $justification,
                                'date_justification' => $statut === StatutPaiement::JUSTIFIEE ? $dateCours->copy()->addDays(rand(1, 3)) : null,
                                'user_id' => $coursItem->user_id, // Le professeur
                                'created_at' => $dateCours,
                                'updated_at' => $dateCours,
                            ]);
                            
                            $absencesCreees++;
                        }
                    }
                }
                
                // Passer à la semaine suivante
                $dateCours->addWeek();
            }
        }
        
        $this->command->info("$absencesCreees absences créées avec succès !");
    }
    
    /**
     * Convertir le jour en français en jour de la semaine Carbon
     */
    private function getJourSemaine(string $jour): int
    {
        $jours = [
            'Lundi' => Carbon::MONDAY,
            'Mardi' => Carbon::TUESDAY,
            'Mercredi' => Carbon::WEDNESDAY,
            'Jeudi' => Carbon::THURSDAY,
            'Vendredi' => Carbon::FRIDAY,
            'Samedi' => Carbon::SATURDAY,
            'Dimanche' => Carbon::SUNDAY,
        ];
        
        return $jours[$jour] ?? Carbon::MONDAY;
    }
    
    /**
     * Obtenir une justification aléatoire
     */
    private function getRandomJustification(): string
    {
        $justifications = [
            'Maladie avec certificat médical',
            'Convocation administrative',
            'Rendez-vous médical',
            'Problème de transport',
            'Raison familiale',
            'Participation à une compétition sportive',
            'Décès dans la famille',
            'Problème de santé',
        ];
        
        return $justifications[array_rand($justifications)];
    }
}
