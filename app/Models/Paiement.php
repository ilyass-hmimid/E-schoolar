<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Paiement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'eleve_id',
        'etudiant_id',
        'matiere_id',
        'pack_id',
        'assistant_id',
        'type',
        'montant',
        'montant_paye',
        'reste',
        'mode_paiement',
        'reference_paiement',
        'date_paiement',
        'statut',
        'commentaires',
        'notes',
        'mois_periode',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'reste' => 'decimal:2',
        'date_paiement' => 'date',
        'mois_periode' => 'date',
    ];

    protected $appends = [
        'statut_libelle',
        'mois_periode_formate',
    ];

    // Statuts possibles pour un paiement
    public const STATUT_PAYE = 'paye';
    public const STATUT_IMPAYE = 'impaye';
    public const STATUT_EN_RETARD = 'en_retard';
    public const STATUT_ANNULE = 'annule';

    // Modes de paiement possibles
    public const MODE_ESPECES = 'especes';
    public const MODE_VIREMENT = 'virement';
    public const MODE_CHEQUE = 'cheque';
    public const MODE_PRELEVEMENT = 'prelevement';

    /**
     * Relation avec l'élève concerné (nouvelle relation)
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id')->withTrashed();
    }

    /**
     * Relation avec l'étudiant (ancienne relation)
     */
    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id')->withTrashed();
    }

    /**
     * Relation avec la matière
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    /**
     * Relation avec le pack
     */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }

    /**
     * Relation avec l'assistant
     */
    public function assistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistant_id')->withTrashed();
    }

    /**
     * Relation avec l'utilisateur qui a créé le paiement
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a modifié le paiement
     */
    public function modificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Accessor pour le libellé du statut
     */
    public function getStatutLibelleAttribute(): string
    {
        return [
            self::STATUT_PAYE => 'Payé',
            self::STATUT_IMPAYE => 'Impayé',
            self::STATUT_EN_RETARD => 'En retard',
            self::STATUT_ANNULE => 'Annulé',
        ][$this->statut] ?? 'Inconnu';
    }

    /**
     * Accessor pour le formatage du mois de période
     */
    public function getMoisPeriodeFormateAttribute(): string
    {
        return $this->mois_periode 
            ? Carbon::parse($this->mois_periode)->format('m/Y')
            : '';
    }

    /**
     * Scope pour les paiements payés
     */
    public function scopePayes($query)
    {
        return $query->where('statut', self::STATUT_PAYE);
    }

    /**
     * Scope pour les paiements impayés
     */
    public function scopeImpayes($query)
    {
        return $query->where('statut', self::STATUT_IMPAYE);
    }

    /**
     * Scope pour les paiements en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->where('statut', self::STATUT_EN_RETARD);
    }

    /**
     * Scope pour les paiements d'un mois et d'une année donnés
     */
    public function scopePourMois($query, $mois, $annee)
    {
        return $query->whereYear('mois_periode', $annee)
                    ->whereMonth('mois_periode', $mois);
    }

    /**
     * Scope pour les paiements d'un élève donné
     */
    public function scopePourEleve($query, $eleveId)
    {
        return $query->where('eleve_id', $eleveId);
    }

    /**
     * Vérifie si le paiement est en retard
     */
    public function estEnRetard(): bool
    {
        if ($this->statut === self::STATUT_PAYE || $this->statut === self::STATUT_ANNULE) {
            return false;
        }

        return $this->mois_periode && $this->mois_periode->isPast();
    }

    /**
     * Marque le paiement comme payé
     */
    public function marquerCommePaye(array $donnees = []): bool
    {
        $this->statut = self::STATUT_PAYE;
        $this->date_paiement = $donnees['date_paiement'] ?? now();
        $this->mode_paiement = $donnees['mode_paiement'] ?? self::MODE_ESPECES;
        $this->reference = $donnees['reference'] ?? null;
        $this->commentaire = $donnees['commentaire'] ?? null;
        
        if (auth()->check()) {
            $this->updated_by = auth()->id();
        }

        return $this->save();
    }
}
