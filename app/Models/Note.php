<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'professeur_id',
        'type',
        'titre',
        'description',
        'note',
        'coefficient',
        'date_evaluation',
        'trimestre',
        'commentaires',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'note' => 'decimal:2',
        'coefficient' => 'decimal:1',
        'date_evaluation' => 'date',
    ];

    /**
     * Relations
     */
    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    /**
     * Scopes
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeParTrimestre($query, $trimestre)
    {
        return $query->where('trimestre', $trimestre);
    }

    public function scopeParEtudiant($query, $etudiantId)
    {
        return $query->where('etudiant_id', $etudiantId);
    }

    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    public function scopeParProfesseur($query, $professeurId)
    {
        return $query->where('professeur_id', $professeurId);
    }

    /**
     * Scope pour filtrer les notes par date ou par plage de dates
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $startDate
     * @param string|\Carbon\Carbon|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParDate($query, $startDate, $endDate = null)
    {
        if ($endDate === null) {
            return $query->whereDate('date_evaluation', $startDate);
        }
        
        return $query->whereBetween('date_evaluation', [
            $startDate,
            $endDate
        ]);
    }

    /**
     * Méthodes utilitaires
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'controle' => 'Contrôle',
            'devoir' => 'Devoir',
            'examen' => 'Examen',
            'composition' => 'Composition',
            default => 'Inconnu',
        };
    }

    public function getNoteSurVingtAttribute(): float
    {
        return round($this->note, 2);
    }

    public function getNotePondereeAttribute(): float
    {
        return round($this->note * $this->coefficient, 2);
    }

    public function getAppreciationAttribute(): string
    {
        if ($this->note >= 16) return 'Très bien';
        if ($this->note >= 14) return 'Bien';
        if ($this->note >= 12) return 'Assez bien';
        if ($this->note >= 10) return 'Passable';
        if ($this->note >= 8) return 'Insuffisant';
        return 'Très insuffisant';
    }

    public function getAppreciationClassAttribute(): string
    {
        if ($this->note >= 16) return 'text-green-600';
        if ($this->note >= 14) return 'text-blue-600';
        if ($this->note >= 12) return 'text-yellow-600';
        if ($this->note >= 10) return 'text-orange-600';
        return 'text-red-600';
    }
}
