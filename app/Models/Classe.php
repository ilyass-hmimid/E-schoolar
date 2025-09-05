<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'niveau_id',
        'filiere_id',
        'professeur_principal_id',
        'annee_scolaire',
        'effectif_max',
        'est_actif',
    ];
    
    protected $casts = [
        'effectif_max' => 'integer',
        'est_actif' => 'boolean',
    ];
    
    protected $appends = [
        'eleves_count',
        'nom_complet',
        'est_complete',
    ];
    
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }
    
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }
    
    public function professeurPrincipal(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'professeur_principal_id');
    }
    
    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'classe_id');
    }
    
    public function getElevesCountAttribute(): int
    {
        return $this->eleves()->count();
    }
    
    public function getNomCompletAttribute(): string
    {
        $parts = [];
        if ($this->niveau) {
            $parts[] = $this->niveau->nom;
        }
        if ($this->filiere) {
            $parts[] = $this->filiere->nom;
        }
        $parts[] = $this->nom;
        
        return implode(' ', $parts);
    }
    
    public function getEstCompleteAttribute(): bool
    {
        if (is_null($this->effectif_max)) {
            return false;
        }
        return $this->eleves_count >= $this->effectif_max;
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'classe_id');
    }
}
