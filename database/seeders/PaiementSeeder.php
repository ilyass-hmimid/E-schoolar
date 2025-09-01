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

        // Récupérer les étudiants avec leurs classes
        $etudiants = Etudiant::with(['user', 'classe'])->get();
        
        if ($etudiants->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter EtudiantSeeder pour créer des étudiants.');
            return;
        }

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
        
        foreach ($etudiants as $etudiant) {
            $dateInscription = $etudiant->date_inscription ?? now()->subMonths(rand(1, 12));
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
                        // Déterminer si le paiement est payé, en retard ou impayé
                        $statut = StatutPaiement::PAYE;
                        $datePaiement = (clone $dateDebut)->addDays(rand(0, 15));
                        
                        // 20% de chance d'être en retard
                        if (rand(1, 100) <= 20) {
                            $statut = StatutPaiement::EN_RETARD;
                            $datePaiement = (clone $dateDebut)->addDays(rand(16, 60));
                            
                            // 50% de chance d'être impayé si en retard
                            if (rand(1, 100) <= 50) {
                                $statut = StatutPaiement::IMPAYE;
                                $datePaiement = null;
                            }
                        }
                        
                        // Créer le paiement
                        Paiement::create([
                            'etudiant_id' => $etudiant->id,
                            'classe_id' => $etudiant->classe_id,
                            'libelle' => $libelle,
                            'montant' => $montant,
                            'montant_paye' => $statut === StatutPaiement::PAYE ? $montant : ($statut === StatutPaiement::EN_RETARD ? $montant * 0.8 : 0),
                            'date_paiement' => $datePaiement,
                            'date_echeance' => (clone $dateDebut)->endOfMonth(),
                            'methode_paiement' => $datePaiement ? $this->getRandomMethodePaiement() : null,
                            'statut' => $statut,
                            'reference' => 'PAY-' . strtoupper(uniqid()),
                            'notes' => $statut === StatutPaiement::EN_RETARD ? 'Relance nécessaire' : null,
                            'annee_scolaire' => $anneeScolaire,
                            'created_at' => $datePaiement ?? $dateDebut,
                            'updated_at' => $datePaiement ?? $dateDebut,
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
        $methodes = [
            MethodePaiement::ESPECES,
            MethodePaiement::CHEQUE,
            MethodePaiement::VIREMENT,
            MethodePaiement::CARTE_BANCAIRE,
            MethodePaiement::PAIEMENT_EN_LIGNE,
        ];
        
        return $methodes[array_rand($methodes)];
    }
}
