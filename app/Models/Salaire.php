<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Salaire extends Model
{
    // Statuts possibles pour un salaire
    public const STATUT_PAYE = 'paye';
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_ANNULE = 'annule';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'professeur_id',
        'montant',
        'date_paiement',
        'periode_debut',
        'periode_fin',
        'statut',
        'commentaire',
        'mode_paiement',
        'reference_paiement',
        'preuve_paiement',  // Chemin vers le fichier de preuve
        'paye_par',         // ID de l'admin qui a effectué le paiement
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'date_paiement' => 'date',
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'montant' => 'decimal:2',
    ];

    /**
     * Relation avec le professeur
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id')
            ->where('role', User::ROLE_PROFESSEUR);
    }

    /**
     * Relation avec l'admin qui a effectué le paiement
     */
    public function payePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paye_par')
            ->where('role', User::ROLE_ADMIN);
    }

    /**
     * Vérifie si le salaire a été payé
     */
    public function getEstPayeAttribute(): bool
    {
        return $this->statut === self::STATUT_PAYE;
    }

    /**
     * Marquer le salaire comme payé
     */
    public function marquerCommePaye(
        float $montant,
        string $modePaiement,
        string $reference,
        int $adminId,
        ?string $commentaire = null
    ): void {
        $this->update([
            'montant' => $montant,
            'mode_paiement' => $modePaiement,
            'reference_paiement' => $reference,
            'date_paiement' => now(),
            'statut' => self::STATUT_PAYE,
            'paye_par' => $adminId,
            'commentaire' => $commentaire,
        ]);
    }

    /**
     * Calculer le salaire d'un professeur pour une période donnée
     * 
     * @param int $professeurId
     * @param string $dateDebut
     * @param string $dateFin
     * @return float
     */
    public static function calculerSalaire(int $professeurId, string $dateDebut, string $dateFin): float
    {
        $professeur = User::findOrFail($professeurId);
        
        // Récupérer toutes les matières enseignées par le professeur
        $matieres = $professeur->matieresEnseignees;
        $totalSalaire = 0;

        foreach ($matieres as $matiere) {
            // Nombre d'élèves dans la matière
            $nombreEleves = $matiere->eleves()->count();
            
            // Calculer le salaire pour cette matière
            $salaireMatiere = $nombreEleves * $matiere->prix_mensuel * ($professeur->pourcentage_remuneration / 100);
            $totalSalaire += $salaireMatiere;
        }

        return round($totalSalaire, 2);
    }

    /**
     * Scope pour les salaires d'un professeur donné
     */
    public function scopePourProfesseur($query, int $professeurId)
    {
        return $query->where('professeur_id', $professeurId);
    }

    /**
     * Scope pour les salaires d'une période donnée
     */
    public function scopeEntreDates($query, string $dateDebut, string $dateFin)
    {
        return $query->where('periode_debut', '>=', $dateDebut)
                    ->where('periode_fin', '<=', $dateFin);
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getLibelleStatutAttribute(): string
    {
        return [
            self::STATUT_PAYE => 'Payé',
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_ANNULE => 'Annulé',
        ][$this->statut] ?? 'Inconnu';
    }

    /**
     * Obtenir le libellé du mode de paiement
     */
    public function getLibelleModePaiementAttribute(): string
    {
        return [
            Paiement::MODE_ESPECES => 'Espèces',
            Paiement::MODE_VIREMENT => 'Virement',
            Paiement::MODE_CHEQUE => 'Chèque',
            Paiement::MODE_CMI => 'Paiement en ligne (CMI)',
        ][$this->mode_paiement] ?? 'Non spécifié';
    }
}
