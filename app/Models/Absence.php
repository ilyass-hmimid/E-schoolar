<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'absences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'professeur_id',
        'assistant_id',
        'date_absence',
        'heure_debut',
        'heure_fin',
        'type',
        'duree_retard',
        'motif',
        'justifiee',
        'justification',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_absence' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'duree_retard' => 'integer',
        'justifiee' => 'boolean',
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

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }

    /**
     * Scopes
     */
    public function scopeAbsences($query)
    {
        return $query->where('type', 'absence');
    }

    public function scopeRetards($query)
    {
        return $query->where('type', 'retard');
    }

    public function scopeJustifiees($query)
    {
        return $query->where('justifiee', true);
    }

    public function scopeNonJustifiees($query)
    {
        return $query->where('justifiee', false);
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
}
