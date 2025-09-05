<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Absence extends Model
{
    use SoftDeletes;

    // Types d'absence
    public const TYPE_COURS = 'cours';
    public const TYPE_EXAMEN = 'examen';
    public const TYPE_AUTRE = 'autre';

    // Statuts d'absence
    public const STATUT_JUSTIFIEE = 'justifiee';
    public const STATUT_NON_JUSTIFIEE = 'non_justifiee';
    public const STATUT_EN_ATTENTE = 'en_attente';

    protected $fillable = [
        'eleve_id',
        'matiere_id',
        'professeur_id',
        'date_absence',
        'type',
        'statut',
        'motif',
        'justificatif',
        'duree',
        'commentaire',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_absence' => 'date',
        'duree' => 'integer', // Durée en minutes
    ];

    protected $appends = [
        'duree_formatee',
        'est_justifiee',
    ];

    /**
     * Relation avec l'élève
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    /**
     * Relation avec la matière
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Relation avec le professeur
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class);
    }

    /**
     * Relation avec la classe via l'élève
     */
    public function classe()
    {
        return $this->eleve->classe();
    }

    /**
     * Relation avec l'utilisateur qui a créé l'absence
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour l'absence
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope pour les absences justifiées
     */
    public function scopeJustifiees(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_JUSTIFIEE);
    }

    /**
     * Scope pour les absences non justifiées
     */
    public function scopeNonJustifiees(Builder $query): Builder
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
     */
    public function scopePourEleve(Builder $query, int $eleveId): Builder
    {
        return $query->where('eleve_id', $eleveId);
    }

    /**
     * Scope pour les absences d'une matière
     */
    public function scopePourMatiere(Builder $query, int $matiereId): Builder
    {
        return $query->where('matiere_id', $matiereId);
    }

    /**
     * Scope pour les absences entre deux dates
     */
    public function scopeEntreDates(Builder $query, string $dateDebut, string $dateFin): Builder
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

    /**
     * Vérifier si l'absence est justifiée
     */
    public function getEstJustifieeAttribute(): bool
    {
        return $this->statut === self::STATUT_JUSTIFIEE;
    }
}
