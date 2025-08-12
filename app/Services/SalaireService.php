<?php

namespace App\Services;

use App\Models\Professeur;
use App\Models\Salaires;
use App\Models\Valeurs_Salaires;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaireService
{
    /**
     * Calcule le salaire d'un professeur en fonction du montant total et du pourcentage
     */
    public function calculerSalaire(float $montantTotal, float $pourcentage): array
    {
        $salaire = ($montantTotal * $pourcentage) / 100;
        
        return [
            'montant_total' => $montantTotal,
            'pourcentage' => $pourcentage,
            'salaire_brut' => $salaire,
        ];
    }

    /**
     * Crée un nouveau salaire pour un professeur
     */
    public function creerSalaire(Professeur $professeur, array $data): Salaires
    {
        return DB::transaction(function () use ($professeur, $data) {
            $valeurSalaire = Valeurs_Salaires::findOrFail($data['valeur_salaire_id']);
            $montantTotal = (float)$data['montant_total'];
            $montantVerse = (float)$data['montant_verse'];
            $reste = max(0, $montantTotal - $montantVerse);
            $etat = $reste <= 0 ? 'payé' : 'partiel';
            $pourcentage = $valeurSalaire->Pourcentage;

            $salaire = Salaires::create([
                'IdProf' => $professeur->id,
                'Montant' => $montantTotal,
                'Montant_actuel' => $montantVerse,
                'Reste' => $reste,
                'Pourcentage' => $pourcentage,
                'Etat' => $etat,
                'Date_Salaire' => $data['date_salaire'] ?? now(),
                'Notes' => $data['notes'] ?? null,
            ]);

            return $salaire;
        });
    }

    /**
     * Met à jour un salaire existant
     */
    public function mettreAJourSalaire(Salaires $salaire, array $data): Salaires
    {
        return DB::transaction(function () use ($salaire, $data) {
            $montantTotal = (float)$data['montant_total'];
            $montantVerse = (float)$data['montant_verse'];
            $reste = max(0, $montantTotal - $montantVerse);
            $etat = $reste <= 0 ? 'payé' : 'partiel';
            $pourcentage = (float)$data['pourcentage'];

            $salaire->update([
                'Montant' => $montantTotal,
                'Montant_actuel' => $montantVerse,
                'Reste' => $reste,
                'Pourcentage' => $pourcentage,
                'Etat' => $etat,
                'Date_Salaire' => $data['date_salaire'] ?? $salaire->Date_Salaire,
                'Notes' => $data['notes'] ?? $salaire->Notes,
            ]);

            return $salaire;
        });
    }

    /**
     * Calcule le solde actuel d'un professeur
     */
    public function calculerSoldeProfesseur(Professeur $professeur): array
    {
        $totalSalaire = $professeur->salaires()->sum('Montant');
        $totalPaye = $professeur->salaires()->sum('Montant_actuel');
        $resteAPayer = $totalSalaire - $totalPaye;

        return [
            'total_salaire' => $totalSalaire,
            'total_paye' => $totalPaye,
            'reste_a_payer' => $resteAPayer,
            'est_a_jour' => $resteAPayer <= 0,
        ];
    }

    /**
     * Génère un rapport des salaires pour une période donnée
     */
    public function genererRapportSalaires(\DateTime $debut, \DateTime $fin): array
    {
        $salaires = Salaires::with(['professeur' => function($query) {
                $query->select('id', 'Nom', 'Prenom');
            }])
            ->whereBetween('Date_Salaire', [$debut, $fin])
            ->orderBy('Date_Salaire', 'desc')
            ->get();

        $totalSalaires = $salaires->sum('Montant_actuel');
        $nombreSalaires = $salaires->count();
        $salairesParMois = $salaires->groupBy(function($item) {
            return Carbon::parse($item->Date_Salaire)->format('Y-m');
        })->map(function($groupe) {
            return $groupe->sum('Montant_actuel');
        });

        return [
            'periode' => [
                'debut' => $debut->format('d/m/Y'),
                'fin' => $fin->format('d/m/Y'),
            ],
            'total_salaires' => $totalSalaires,
            'nombre_salaires' => $nombreSalaires,
            'salaires_par_mois' => $salairesParMois,
            'details' => $salaires->map(function($salaire) {
                return [
                    'id' => $salaire->id,
                    'professeur' => $salaire->professeur ? $salaire->professeur->Nom . ' ' . $salaire->professeur->Prenom : 'Inconnu',
                    'montant_total' => $salaire->Montant,
                    'montant_paye' => $salaire->Montant_actuel,
                    'reste' => $salaire->Reste,
                    'pourcentage' => $salaire->Pourcentage . '%',
                    'etat' => $salaire->Etat,
                    'date_salaire' => Carbon::parse($salaire->Date_Salaire)->format('d/m/Y'),
                    'date_creation' => $salaire->created_at->format('d/m/Y H:i'),
                ];
            }),
        ];
    }
}
