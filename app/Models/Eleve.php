<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eleve extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'classe_id',
        'date_naissance',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'nom_pere',
        'profession_pere',
        'telephone_pere',
        'nom_mere',
        'profession_mere',
        'telephone_mere',
        'notes',
        'est_actif',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'est_actif' => 'boolean',
    ];

    /**
     * Get the classe that owns the eleve.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Get the paiements for the eleve.
     */
    public function paiements()
    {
        return $this->hasMany(PaiementEleve::class)->orderBy('mois', 'desc');
    }
    
    /**
     * Get the active paiements for the eleve.
     */
    public function paiementsActifs()
    {
        return $this->paiements()->where('statut', 'paye');
    }
    
    /**
     * Get the pending paiements for the eleve.
     */
    public function paiementsEnAttente()
    {
        return $this->paiements()->whereIn('statut', ['en_retard', 'impaye']);
    }
    
    /**
     * Get the full name of the student.
     */
    public function getNomCompletAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }
    
    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Get the absences for the eleve.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'eleve_id')
            ->orderBy('date_debut', 'desc');
    }
    
    /**
     * Get the justified absences for the eleve.
     */
    public function absencesJustifiees(): HasMany
    {
        return $this->absences()->where('est_justifiee', true);
    }
    
    /**
     * Get the unjustified absences for the eleve.
     */
    public function absencesNonJustifiees(): HasMany
    {
        return $this->absences()->where('est_justifiee', false);
    }
    
    /**
     * Get the absences by status.
     */
    public function absencesParStatut(string $statut): HasMany
    {
        return $this->absences()->where('statut', $statut);
    }
    
    /**
     * Get the absences count by status.
     */
    public function getNombreAbsencesParStatutAttribute(): array
    {
        return [
            'total' => $this->absences()->count(),
            'justifiees' => $this->absencesJustifiees()->count(),
            'non_justifiees' => $this->absencesNonJustifiees()->count(),
            'en_attente' => $this->absencesParStatut('en_attente')->count(),
            'validees' => $this->absencesParStatut('validee')->count(),
            'rejetees' => $this->absencesParStatut('rejetee')->count(),
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
}
