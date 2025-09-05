<?php

namespace Database\Seeders;

use App\Models\Paiement;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Enums\StatutPaiement;
use App\Enums\MethodePaiement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des paiements
        DB::table('paiements')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Récupérer tous les élèves avec leur utilisateur associé
        $eleves = DB::table('eleves')
            ->join('users', 'eleves.user_id', '=', 'users.id')
            ->select('eleves.id as eleve_id', 'users.id as user_id', 'users.name')
            ->get();
            
        if ($eleves->isEmpty()) {
            $this->command->error('Aucun élève trouvé. Veuillez d\'abord exécuter EleveSeeder pour créer des élèves.');
            return;
        }
        
        $this->command->info('Nombre d\'élèves trouvés : ' . $eleves->count());

        // Mois de l'année scolaire (de septembre à juin)
        $moisAnneeScolaire = [
            'Septembre', 'Octobre', 'Novembre', 'Décembre',
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'
        ];
        
        // Types de frais
        $typesFrais = [
            ['libelle' => 'Frais de scolarité', 'montant' => 800, 'periode' => 'Mensuel'],
            ['libelle' => 'Frais d\'inscription', 'montant' => 500, 'periode' => 'Annuel'],
            ['libelle' => 'Frais de matériel', 'montant' => 300, 'periode' => 'Trimestriel'],
            ['libelle' => 'Frais d\'examen', 'montant' => 200, 'periode' => 'Semestriel'],
        ];
        
        $paiementsCrees = 0;
        $anneeScolaire = (date('m') >= 9 ? date('Y') : date('Y') - 1) . '-' . (date('m') >= 9 ? date('Y') + 1 : date('Y'));
        
        foreach ($eleves as $eleve) {
            $dateInscription = now()->subMonths(rand(1, 12));
            $dateDebut = Carbon::parse($dateInscription)->startOfMonth();
            $dateActuelle = now();
            
            // Générer les paiements pour chaque mois depuis l'inscription
            while ($dateDebut->lte($dateActuelle)) {
                // Pour chaque type de frais, déterminer s'il doit être facturé ce mois-ci
                foreach ($typesFrais as $type) {
                    $doitPayer = false;
                    $libelle = $type['libelle'];
                    $montant = $type['montant'];
                    
                    // Vérifier si ce type de frais doit être facturé ce mois-ci
                    if ($type['periode'] === 'Mensuel') {
                        $doitPayer = true;
                        $libelle .= ' - ' . $dateDebut->format('F Y');
                    } 
                    elseif ($type['periode'] === 'Trimestriel' && $dateDebut->month % 3 === 0) {
                        $doitPayer = true;
                        $trimestre = ceil($dateDebut->month / 3);
                        $libelle .= " - Trimestre $trimestre " . $dateDebut->format('Y');
                    }
                    elseif ($type['periode'] === 'Semestriel' && ($dateDebut->month === 1 || $dateDebut->month === 9)) {
                        $doitPayer = true;
                        $semestre = $dateDebut->month === 1 ? '1er Semestre' : '2ème Semestre';
                        $libelle .= " - $semestre " . $dateDebut->format('Y');
                    }
                    elseif ($type['periode'] === 'Annuel' && $dateDebut->month === 9) {
                        $doitPayer = true;
                        $libelle .= " - Année Scolaire $anneeScolaire";
                    }
                    
                    if ($doitPayer) {
                        // Déterminer le statut du paiement selon les valeurs de la base de données
                        // Valeurs possibles : 'en_attente', 'valide', 'annule'
                        $statut = 'valide'; // Par défaut, le paiement est valide
                        $datePaiement = (clone $dateDebut)->addDays(rand(0, 15));
                        
                        // 20% de chance d'être en attente
                        if (rand(1, 100) <= 20) {
                            $statut = 'en_attente';
                            // Utiliser la date actuelle pour les paiements en attente
                            $datePaiement = now();
                            
                            // 50% de chance d'être annulé si en attente
                            if (rand(1, 100) <= 50) {
                                $statut = 'annule';
                                // Utiliser la date actuelle pour les paiements annulés
                                $datePaiement = now();
                            }
                        }
                        
                        // Sélectionner une matière aléatoire
                        $matiere = DB::table('matieres')->inRandomOrder()->first();
                        $matiereId = $matiere ? $matiere->id : 1;
                        
                        // Créer le paiement
                        // Calculate montant_paye and reste based on status
                        $montantPaye = in_array($statut, ['valide', 'partiel']) ? $montant : 0;
                        $reste = $montant - $montantPaye;
                        
                        // If status is 'partiel', set montant_paye to a random amount less than montant
                        if ($statut === 'partiel') {
                            $montantPaye = round($montant * (rand(50, 90) / 100), 2);
                            $reste = $montant - $montantPaye;
                        }
                        
                        Paiement::create([
                            'user_id' => $eleve->user_id, // Clé étrangère vers users
                            'etudiant_id' => $eleve->user_id, // Pour rétrocompatibilité
                            'eleve_id' => $eleve->eleve_id, // Clé étrangère vers eleves
                            'matiere_id' => $matiereId,
                            'pack_id' => null, // Peut être défini plus tard si nécessaire
                            'assistant_id' => null, // Peut être défini plus tard si nécessaire
                            'type' => 'scolarite', // Type de paiement
                            'montant' => $montant,
                            'montant_paye' => $montantPaye,
                            'reste' => $reste,
                            'mode_paiement' => $this->getRandomMethodePaiement(),
                            'reference_paiement' => 'PAY-' . strtoupper(Str::random(10)),
                            'date_paiement' => $datePaiement,
                            'statut' => $statut,
                            'commentaires' => 'Paiement généré automatiquement',
                            'notes' => 'Période: ' . $dateDebut->format('F Y'),
                            'mois_periode' => $dateDebut->format('Y-m'),
                            'created_at' => $dateDebut,
                            'updated_at' => $dateDebut,
                        ]);
                        
                        $paiementsCrees++;
                    }
                }
                
                // Passer au mois suivant
                $dateDebut->addMonth();
            }
        }
        
        $this->command->info("$paiementsCrees paiements créés avec succès !");
    }
    
    /**
     * Obtenir une méthode de paiement aléatoire
     */
    private function getRandomMethodePaiement(): string
    {
        // Méthodes de paiement possibles selon le schéma de la base de données
        $methodes = ['especes', 'cheque', 'virement', 'carte'];
        return $methodes[array_rand($methodes)];
    }
}
