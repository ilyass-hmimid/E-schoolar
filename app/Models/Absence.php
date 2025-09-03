<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    // Statuts de justification
    public const STATUT_JUSTIFICATION = [
        'en_attente' => 'En attente',
        'justifiee' => 'Justifiée',
        'non_justifiee' => 'Non justifiée',
        'en_cours' => 'En cours de traitement'
    ];
    
    use SoftDeletes;

    protected $table = 'absences';

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'date_debut',
        'date_fin',
        'type',
        'motif',
        'est_justifiee',
        'justification',
        'piece_jointe',
        'statut',
        'enregistre_par',
        'valide_par',
        'valide_le',
        'commentaires_validation',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_justifiee' => 'boolean',
        'valide_le' => 'datetime',
    ];

    protected $appends = [
        'duree',
        'statut_label',
    ];

    /**
     * Get the eleve that owns the absence.
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    /**
     * Get the classe that owns the absence.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Get the user who recorded the absence.
     */
    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    /**
     * Get the user who validated the absence.
     */
    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Calculate the duration of the absence in days.
     */
    public function getDureeAttribute(): int
    {
        if (!$this->date_fin) {
            return 1;
        }
        
        return $this->date_debut->diffInDays($this->date_fin) + 1;
    }

    /**
     * Get the status label for the absence.
     */
    public function getStatutLabelAttribute(): string
    {
        return [
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
        ][$this->statut] ?? $this->statut;
    }

    /**
     * Scopes
     */
    
    /**
     * Scope for absences (not delays)
     */
    public function scopeAbsences($query)
    {
        return $query->where('type', 'absence');
    }
    
    /**
     * Scope for delays
     */
    public function scopeRetards($query)
    {
        return $query->where('type', 'retard');
    }
    
    /**
     * Scope for justified absences
     */
    public function scopeJustifiees($query)
    {
        return $query->where('est_justifiee', true);
    }
    
    /**
     * Scope for pending validation absences
     */
    public function scopeEnAttenteValidation($query)
    {
        return $query->where('statut', 'en_attente');
    }
    
    /**
     * Scope for validated absences
     */
    public function scopeValidees($query)
    {
        return $query->where('statut', 'validee');
    }
    
    /**
     * Scope for rejected absences
     */
    public function scopeRejetees($query)
    {
        return $query->where('statut', 'rejetee');
    }
    
    /**
     * Scope for non-justified absences
     */
    public function scopeNonJustifiees($query)
    {
        return $query->where('est_justifiee', false);
    }

    /**
     * Scope pour filtrer les absences par date ou par plage de dates
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $startDate
     * @param string|\Carbon\Carbon|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParDate($query, $startDate, $endDate = null)
    {
        if ($endDate === null) {
            return $query->whereDate('date_absence', $startDate);
        }
        
        return $query->whereBetween('date_absence', [
            $startDate,
            $endDate
        ]);
    }

    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    public function scopeParEtudiant($query, $etudiantId)
    {
        return $query->where('etudiant_id', $etudiantId);
    }
    
    /**
     * Accessor pour l'URL de la pièce jointe
     *
     * @return string|null
     */
    public function getPieceJointeUrlAttribute()
    {
        return $this->piece_jointe ? Storage::url($this->piece_jointe) : null;
    }
    
    /**
     * Vérifie si l'absence a une pièce jointe
     *
     * @return bool
     */
    public function getAPieceJointeAttribute()
    {
        return !empty($this->piece_jointe);
    }
    
    /**
     * Obtient la durée formatée du retard
     *
     * @return string
     */
    public function getDureeRetardFormateeAttribute()
    {
        if (!$this->duree_retard) {
            return '-';
        }
        
        $heures = floor($this->duree_retard / 60);
        $minutes = $this->duree_retard % 60;
        
        if ($heures > 0) {
            return sprintf('%dh%02d', $heures, $minutes);
        }
        
        return $minutes . ' min';
    }
    
}
