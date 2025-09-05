<?php

namespace App\Services;

use App\Models\PaiementProf;
use App\Models\Professeur;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Calculate payments for all professors for a given period
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function calculateMonthlyPayments(Carbon $startDate, Carbon $endDate): array
    {
        $results = [];
        
        // Get all professors with their matieres and pourcentage
        $professeurs = Professeur::with(['matieres' => function($query) {
            $query->select('matieres.id', 'nom', 'prix_par_eleve')
                  ->withPivot('pourcentage');
        }])->get();

        foreach ($professeurs as $professeur) {
            foreach ($professeur->matieres as $matiere) {
                // Get number of students for this professor's matiere
                $nbreEleves = $this->countStudentsForProfesseurMatiere($professeur->id, $matiere->id);
                
                if ($nbreEleves === 0) {
                    continue;
                }

                // Calculate total amount and professor's share
                $montantTotal = $nbreEleves * $matiere->prix_par_eleve;
                $pourcentage = (float) $matiere->pivot->pourcentage;
                $montantProf = ($montantTotal * $pourcentage) / 100;

                // Check if payment already exists for this period
                $existingPayment = PaiementProf::where('professeur_id', $professeur->id)
                    ->where('matiere_id', $matiere->id)
                    ->where('date_debut', $startDate->format('Y-m-d'))
                    ->where('date_fin', $endDate->format('Y-m-d'))
                    ->first();

                if ($existingPayment) {
                    // Update existing payment
                    $existingPayment->update([
                        'nbre_eleves' => $nbreEleves,
                        'montant_total' => $montantTotal,
                        'pourcentage' => $pourcentage,
                        'montant_prof' => $montantProf,
                    ]);
                    
                    $results[] = [
                        'professeur' => $professeur->name,
                        'matiere' => $matiere->nom,
                        'status' => 'updated',
                        'montant' => $montantProf
                    ];
                } else {
                    // Create new payment
                    PaiementProf::create([
                        'professeur_id' => $professeur->id,
                        'matiere_id' => $matiere->id,
                        'nbre_eleves' => $nbreEleves,
                        'montant_total' => $montantTotal,
                        'pourcentage' => $pourcentage,
                        'montant_prof' => $montantProf,
                        'date_debut' => $startDate->format('Y-m-d'),
                        'date_fin' => $endDate->format('Y-m-d'),
                        'statut' => 'en_attente',
                    ]);
                    
                    $results[] = [
                        'professeur' => $professeur->name,
                        'matiere' => $matiere->nom,
                        'status' => 'created',
                        'montant' => $montantProf
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Count number of students for a professor's matiere
     *
     * @param int $professeurId
     * @param int $matiereId
     * @return int
     */
    protected function countStudentsForProfesseurMatiere(int $professeurId, int $matiereId): int
    {
        return DB::table('enseignements')
            ->join('inscriptions', function($join) use ($matiereId) {
                $join->on('enseignements.classe_id', '=', 'inscriptions.classe_id')
                     ->where('enseignements.matiere_id', '=', $matiereId);
            })
            ->where('enseignements.professeur_id', $professeurId)
            ->where('inscriptions.est_actif', true)
            ->count();
    }

    /**
     * Mark payments as paid
     *
     * @param array $paymentIds
     * @return int
     */
    public function markAsPaid(array $paymentIds): int
    {
        return PaiementProf::whereIn('id', $paymentIds)
            ->update([
                'statut' => 'paye',
                'date_paiement' => now(),
            ]);
    }
}
