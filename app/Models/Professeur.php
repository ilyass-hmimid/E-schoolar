<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professeur extends Enseignant
{
    protected $table = 'professeurs';
    
    protected $appends = [
        'nom_complet',
        'anciennete',
    ];

    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignements', 'professeur_id', 'matiere_id');
    }
    
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'enseignements', 'professeur_id', 'classe_id');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'professeur_id');
    }

    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function getAncienneteAttribute(): ?int
    {
        if (!$this->date_embauche) {
            return null;
        }
        
        return $this->date_embauche->diffInYears(now());
    }
}
