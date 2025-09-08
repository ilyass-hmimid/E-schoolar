<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Absence extends Model
{
    // Statuts d'absence
    public const STATUT_JUSTIFIEE = 'justifiee';
    public const STATUT_NON_JUSTIFIEE = 'non_justifiee';
    public const STATUT_EN_ATTENTE = 'en_attente';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'eleve_id',
        'matiere_id',
        'date_absence',
        'heures_manquees', // Durée en heures (ex: 1.5 pour 1h30)
        'statut',
        'motif',
        'justificatif',
        'commentaire',
        'traite_par',      // ID de l'admin qui a traité la justification
        'date_traitement', // Date de traitement de la justification
        'commentaire_refus', // Si la justification est refusée
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'date_absence' => 'date',
        'date_traitement' => 'datetime',
        'heures_manquees' => 'decimal:1', // 1 chiffre après la virgule (ex: 1.5)
    ];

    /**
     * Les attributs calculés qui doivent être ajoutés au tableau du modèle.
     *
     * @var array
     */
    protected $appends = [
        'est_justifiee',
        'est_en_attente',
    ];

    /**
     * Relation avec l'élève absent
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(User::class, 'eleve_id')
            ->where('role', User::ROLE_ELEVE);
    }

    /**
     * Relation avec la matière concernée
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Relation avec l'utilisateur qui a traité la justification
     */
    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    /**
     * Vérifie si l'absence est justifiée
     */
    public function getEstJustifieeAttribute(): bool
    {
        return $this->statut === self::STATUT_JUSTIFIEE;
    }

    /**
     * Vérifie si la justification est en attente de traitement
     */
    public function getEstEnAttenteAttribute(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    /**
     * Marquer l'absence comme justifiée
     */
    public function marquerCommeJustifiee(int $userId, ?string $commentaire = null): void
    {
        $this->update([
            'statut' => self::STATUT_JUSTIFIEE,
            'traite_par' => $userId,
            'date_traitement' => now(),
            'commentaire' => $commentaire,
        ]);
    }

    /**
     * Marquer l'absence comme non justifiée
     */
    public function marquerCommeNonJustifiee(int $userId, string $raison): void
    {
        $this->update([
            'statut' => self::STATUT_NON_JUSTIFIEE,
            'traite_par' => $userId,
            'date_traitement' => now(),
            'commentaire_refus' => $raison,
        ]);
    }


    /**
     * Scope pour les absences justifiées
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJustifiees($query)
    {
        return $query->where('statut', self::STATUT_JUSTIFIEE);
    }

    /**
     * Scope pour les absences non justifiées
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonJustifiees($query)
    {
        return $query->where('statut', self::STATUT_NON_JUSTIFIEE);
    }

    /**
     * Scope pour les absences en attente de justification
     */
    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    /**
     * Scope pour les absences d'un élève
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
     * Scope pour les absences d'une matière
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $matiereId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePourMatiere($query, int $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    /**
     * Scope pour les absences entre deux dates
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dateDebut
     * @param string $dateFin
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntreDates($query, string $dateDebut, string $dateFin)
    {
        return $query->whereBetween('date_absence', [
            Carbon::parse($dateDebut)->startOfDay(),
            Carbon::parse($dateFin)->endOfDay()
        ]);
    }

    /**
     * Compter les absences d'un élève sur une période
     */
    public static function compterPourEleve(int $eleveId, ?string $dateDebut = null, ?string $dateFin = null): int
    {
        $query = self::where('eleve_id', $eleveId);
        
        if ($dateDebut && $dateFin) {
            $query->whereBetween('date_absence', [
                Carbon::parse($dateDebut)->startOfDay(),
                Carbon::parse($dateFin)->endOfDay()
            ]);
        }
        
        return $query->count();
    }

    /**
     * Obtenir la durée formatée (ex: "2h30")
     */
    public function getDureeFormateeAttribute(): string
    {
        if (!$this->duree) {
            return '';
        }
        
        $heures = floor($this->duree / 60);
        $minutes = $this->duree % 60;
        
        if ($heures > 0 && $minutes > 0) {
            return "{$heures}h" . str_pad($minutes, 2, '0', STR_PAD_LEFT);
        } elseif ($heures > 0) {
            return "{$heures}h";
        } else {
            return "{$minutes}min";
        }
    }

}
