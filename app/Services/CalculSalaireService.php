<?php

namespace App\Services;

use App\Models\Enseignement;
use App\Models\Salaire;
use App\Models\Inscription;
use App\Models\ConfigurationSalaire;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculSalaireService
{
    /**
     * Calcule le salaire d'un professeur pour une matière et un mois donnés
     *
     * @param int $professeurId
     * @param int $matiereId
     * @param string $moisPeriode Format: YYYY-MM
     * @return array
     */
    public function calculerSalaireMatiere($professeurId, $matiereId, $moisPeriode)
    {
        // Récupérer l'enseignement pour cette matière et ce professeur
        $enseignement = Enseignement::where('professeur_id', $professeurId)
            ->where('matiere_id', $matiereId)
            ->where('est_actif', true)
            ->first();

        if (!$enseignement) {
            return [
                'success' => false,
                'message' => 'Enseignement non trouvé pour ce professeur et cette matière.'
            ];
        }

        // Récupérer le nombre d'étudiants inscrits à ce cours pour le mois donné
        $nombreEleves = $this->compterElevesInscrits($enseignement->id, $moisPeriode);
        
        // Récupérer la configuration de salaire pour cette matière
        $configSalaire = ConfigurationSalaire::getForMatiere($matiereId);
        
        // Valider la configuration
        if (!$configSalaire->est_actif) {
            Log::warning("Configuration de salaire désactivée pour la matière ID: $matiereId");
            return [
                'success' => false,
                'message' => 'La configuration de salaire pour cette matière est désactivée.'
            ];
        }
        
        // Calculer les montants
        $montantBrut = $nombreEleves * $configSalaire->prix_unitaire;
        $montantCommission = $montantBrut * ($configSalaire->commission_prof / 100);
        $montantNet = $montantBrut - $montantCommission;

        // Enregistrer ou mettre à jour le salaire dans la base de données
        $salaire = Salaire::updateOrCreate(
            [
                'professeur_id' => $professeurId,
                'matiere_id' => $matiereId,
                'mois_periode' => $moisPeriode,
            ],
            [
                'nombre_eleves' => $nombreEleves,
                'prix_unitaire' => $configSalaire->prix_unitaire,
                'commission_prof' => $configSalaire->commission_prof,
                'montant_brut' => $montantBrut,
                'montant_commission' => $montantCommission,
                'montant_net' => $montantNet,
                'statut' => 'en_attente',
            ]
        );

        return [
            'success' => true,
            'salaire' => $salaire,
            'message' => 'Salaire calculé avec succès.'
        ];
    }

    /**
     * Calcule les salaires de tous les professeurs pour un mois donné
     *
     * @param string $moisPeriode Format: YYYY-MM
     * @return array
     */
    public function calculerTousLesSalaires($moisPeriode)
    {
        $resultats = [];
        
        // Récupérer tous les enseignements actifs
        $enseignements = Enseignement::with(['professeur', 'matiere'])
            ->where('est_actif', true)
            ->get();

        foreach ($enseignements as $enseignement) {
            $resultat = $this->calculerSalaireMatiere(
                $enseignement->professeur_id,
                $enseignement->matiere_id,
                $moisPeriode
            );
            
            $resultats[] = [
                'professeur' => $enseignement->professeur->name,
                'matiere' => $enseignement->matiere->nom,
                'success' => $resultat['success'],
                'message' => $resultat['message'] ?? null,
                'salaire' => $resultat['salaire'] ?? null,
            ];
        }

        return [
            'success' => true,
            'resultats' => $resultats,
            'message' => 'Calcul des salaires terminé pour tous les professeurs.'
        ];
    }

    /**
     * Compte le nombre d'étudiants inscrits à un enseignement pour un mois donné
     *
     * @param int $enseignementId
     * @param string $moisPeriode Format: YYYY-MM
     * @return int
     */
    protected function compterElevesInscrits($enseignementId, $moisPeriode)
    {
        $dateDebut = Carbon::parse($moisPeriode . '-01');
        $dateFin = $dateDebut->copy()->endOfMonth();
        
        return Inscription::where('enseignement_id', $enseignementId)
            ->where(function($query) use ($dateDebut, $dateFin) {
                $query->where(function($q) use ($dateDebut, $dateFin) {
                    // Inscription commencée avant ou pendant le mois et non encore terminée
                    $q->where('date_debut', '<=', $dateFin)
                      ->where(function($q2) use ($dateDebut) {
                          $q2->whereNull('date_fin')
                             ->orWhere('date_fin', '>=', $dateDebut);
                      });
                });
            })
            ->where('statut', 'valide')
            ->count();
    }


    /**
     * Valide et paie un salaire
     *
     * @param int $salaireId
     * @param string $datePaiement
     * @param string $commentaires
     * @return array
     */
    public function validerPaiementSalaire($salaireId, $datePaiement = null, $commentaires = null)
    {
        try {
            $salaire = Salaire::findOrFail($salaireId);
            
            $salaire->update([
                'statut' => 'paye',
                'date_paiement' => $datePaiement ?? now(),
                'commentaires' => $commentaires,
            ]);

            // Ici, vous pourriez ajouter une notification au professeur
            // $salaire->professeur->notify(new PaiementSalaireNotification($salaire));

            return [
                'success' => true,
                'message' => 'Paiement validé avec succès.',
                'salaire' => $salaire
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la validation du paiement: ' . $e->getMessage()
            ];
        }
    }
}
