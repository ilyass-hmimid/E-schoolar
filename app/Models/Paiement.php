<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Paiement extends Model
{
    // Statuts possibles pour un paiement
    public const STATUT_PAYE = 'paye';
    public const STATUT_IMPAYE = 'impaye';
    public const STATUT_EN_RETARD = 'en_retard';
    public const STATUT_ANNULE = 'annule';

    // Types de paiement
    public const TYPE_INSCRIPTION = 'inscription';
    public const TYPE_MENSUALITE = 'mensualite';
    public const TYPE_AUTRE = 'autre';

    // Modes de paiement possibles
    public const MODE_ESPECES = 'especes';
    public const MODE_VIREMENT = 'virement';
    public const MODE_CHEQUE = 'cheque';
    public const MODE_CMI = 'cmi';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'eleve_id',
        'matiere_id',
        'type',
        'montant',
        'mode_paiement',
        'reference_paiement',
        'date_paiement',
        'mois_couvre',          // Mois couvert par le paiement (pour les mensualités)
        'annee_scolaire',       // Année scolaire (ex: 2023-2024)
        'statut',
        'commentaire',
        'preuve_paiement',      // Chemin vers le fichier de preuve (reçu, capture d'écran, etc.)
        'enregistre_par',       // ID de l'admin qui a enregistré le paiement
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'datetime',
        'mois_couvre' => 'date',
    ];
    
    protected $dates = [
        'date_paiement',
        'mois_couvre',
        'created_at',
        'updated_at'
    ];

    /**
     * Relation avec l'élève concerné
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(User::class, 'eleve_id')
            ->where('role', User::ROLE_ELEVE);
    }

    /**
     * Relation avec la matière
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Relation avec l'admin qui a enregistré le paiement
     */
    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par')
            ->where('role', User::ROLE_ADMIN);
    }

    /**
     * Vérifie si le paiement est complet
     */
    public function getEstPayeAttribute(): bool
    {
        return $this->statut === self::STATUT_PAYE;
    }

    /**
     * Vérifie si le paiement est en retard
     */
    public function getEstEnRetardAttribute(): bool
    {
        return $this->statut === self::STATUT_EN_RETARD;
    }



    /**
     * Scope pour les paiements d'une matière donnée
     */
    public function scopePourMatiere($query, int $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    /**
     * Scope pour les paiements d'un type donné
     */
    public function scopeDeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les paiements d'une année scolaire donnée
     */
    public function scopePourAnneeScolaire($query, string $anneeScolaire)
    {
        return $query->where('annee_scolaire', $anneeScolaire);
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getLibelleStatutAttribute(): string
    {
        return [
            self::STATUT_PAYE => 'Payé',
            self::STATUT_IMPAYE => 'Impayé',
            self::STATUT_EN_RETARD => 'En retard',
        ][$this->statut] ?? 'Inconnu';
    }

    /**
     * Obtenir le libellé du mode de paiement
     */
    public function getLibelleModePaiementAttribute(): string
    {
        return [
            self::MODE_ESPECES => 'Espèces',
            self::MODE_VIREMENT => 'Virement',
            self::MODE_CHEQUE => 'Chèque',
            self::MODE_CMI => 'Paiement en ligne (CMI)',
        ][$this->mode_paiement] ?? 'Non spécifié';
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
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $eleveId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePourEleve($query, int $eleveId)
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
     * 
     * @param array|string $donnees Tableau de données ou référence de paiement
     * @param string|null $modePaiement Mode de paiement (si $donnees est une chaîne)
     * @param string|null $commentaire Commentaire optionnel (si $donnees est une chaîne)
     * @return bool
     */
    public function marquerCommePaye($donnees = [], ?string $modePaiement = null, ?string $commentaire = null): bool
    {
        // Support pour l'ancienne signature (string, string, ?string)
        if (is_string($donnees) && !empty($modePaiement)) {
            $donnees = [
                'reference_paiement' => $donnees,
                'mode_paiement' => $modePaiement,
                'commentaire' => $commentaire,
                'date_paiement' => now()
            ];
        }
        
        // S'assure que $donnees est un tableau
        $donnees = is_array($donnees) ? $donnees : [];
        
        // Mise à jour des attributs
        $this->statut = self::STATUT_PAYE;
        
        // Gestion de la date de paiement avec Carbon
        $this->date_paiement = isset($donnees['date_paiement']) 
            ? \Carbon\Carbon::parse($donnees['date_paiement'])
            : now();
        
        $this->mode_paiement = $donnees['mode_paiement'] ?? self::MODE_ESPECES;
        $this->reference_paiement = $donnees['reference_paiement'] ?? $donnees['reference'] ?? null;
        $this->commentaire = $donnees['commentaire'] ?? null;
        
        if (auth()->check()) {
            $this->updated_by = auth()->id();
        }

        return $this->save();
    }
}
