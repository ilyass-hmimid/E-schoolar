<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Paiment;
use App\Models\Valeurs_Paiments;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaiementService
{
    /**
     * Crée un nouveau paiement pour un étudiant
     */
    public function creerPaiement(Etudiant $etudiant, array $data): Paiment
    {
        return DB::transaction(function () use ($etudiant, $data) {
            $valeurPaiment = Valeurs_Paiments::findOrFail($data['valeur_paiment_id']);
            $montantTotal = $valeurPaiment->Valeur;
            $montantPaye = (float)$data['montant_paye'];
            $reste = max(0, $montantTotal - $montantPaye);
            $etat = $reste <= 0 ? 'payé' : 'partiel';

            $paiment = Paiment::create([
                'IdEtu' => $etudiant->id,
                'Montant' => $montantTotal,
                'SommeApaye' => $montantPaye,
                'Reste' => $reste,
                'Etat' => $etat,
                'Date_Paiment' => $data['date_paiment'] ?? now(),
                'Notes' => $data['notes'] ?? null,
            ]);

            // Mettre à jour le solde de l'étudiant
            $etudiant->increment('SommeApaye', $montantPaye);

            return $paiment;
        });
    }

    /**
     * Met à jour un paiement existant
     */
    public function mettreAJourPaiement(Paiment $paiment, array $data): Paiment
    {
        return DB::transaction(function () use ($paiment, $data) {
            $ancienMontantPaye = $paiment->SommeApaye;
            $nouveauMontantPaye = (float)$data['montant_paye'];
            $difference = $nouveauMontantPaye - $ancienMontantPaye;
            
            $reste = max(0, $paiment->Montant - $nouveauMontantPaye);
            $etat = $reste <= 0 ? 'payé' : 'partiel';

            // Mettre à jour le paiement
            $paiment->update([
                'SommeApaye' => $nouveauMontantPaye,
                'Reste' => $reste,
                'Etat' => $etat,
                'Date_Paiment' => $data['date_paiment'] ?? $paiment->Date_Paiment,
                'Notes' => $data['notes'] ?? $paiment->Notes,
            ]);

            // Mettre à jour le solde de l'étudiant
            if ($difference != 0) {
                $etudiant = $paiment->etudiant;
                $etudiant->increment('SommeApaye', $difference);
            }

            return $paiment;
        });
    }

    /**
     * Supprime un paiement et met à jour le solde de l'étudiant
     */
    public function supprimerPaiement(Paiment $paiment): bool
    {
        return DB::transaction(function () use ($paiment) {
            $etudiant = $paiment->etudiant;
            $montantPaye = $paiment->SommeApaye;
            
            // Supprimer le paiement
            $estSupprime = $paiment->delete();
            
            // Mettre à jour le solde de l'étudiant
            if ($estSupprime && $montantPaye > 0) {
                $etudiant->decrement('SommeApaye', $montantPaye);
            }
            
            return $estSupprime;
        });
    }

    /**
     * Calcule le solde actuel d'un étudiant
     */
    public function calculerSoldeEtudiant(Etudiant $etudiant): array
    {
        $totalPaye = $etudiant->paiements()->sum('SommeApaye');
        $totalDu = $etudiant->paiements()->sum('Montant');
        $resteAPayer = $totalDu - $totalPaye;

        return [
            'total_du' => $totalDu,
            'total_paye' => $totalPaye,
            'reste_a_payer' => $resteAPayer,
            'est_a_jour' => $resteAPayer <= 0,
        ];
    }

    /**
     * Génère un rapport des paiements pour une période donnée
     */
    public function genererRapportPaiements(\DateTime $debut, \DateTime $fin): array
    {
        $paiements = Paiment::with(['etudiant' => function($query) {
                $query->select('id', 'Nom', 'Prenom');
            }])
            ->whereBetween('Date_Paiment', [$debut, $fin])
            ->orderBy('Date_Paiment', 'desc')
            ->get();

        $totalPaiements = $paiements->sum('SommeApaye');
        $nombrePaiements = $paiements->count();
        $paiementsParMois = $paiements->groupBy(function($item) {
            return Carbon::parse($item->Date_Paiment)->format('Y-m');
        })->map(function($groupe) {
            return $groupe->sum('SommeApaye');
        });

        return [
            'periode' => [
                'debut' => $debut->format('d/m/Y'),
                'fin' => $fin->format('d/m/Y'),
            ],
            'total_paiements' => $totalPaiements,
            'nombre_paiements' => $nombrePaiements,
            'paiements_par_mois' => $paiementsParMois,
            'details' => $paiements->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'etudiant' => $paiement->etudiant ? $paiement->etudiant->Nom . ' ' . $paiement->etudiant->Prenom : 'Inconnu',
                    'montant' => $paiement->Montant,
                    'montant_paye' => $paiement->SommeApaye,
                    'reste' => $paiement->Reste,
                    'etat' => $paiement->Etat,
                    'date_paiment' => Carbon::parse($paiement->Date_Paiment)->format('d/m/Y'),
                    'date_creation' => $paiement->created_at->format('d/m/Y H:i'),
                ];
            }),
        ];
    }
}
