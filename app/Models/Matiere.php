<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matiere extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'description',
        'coefficient',
        'niveau_id',
        'est_obligatoire',
        'volume_horaire',
        'couleur',
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'volume_horaire' => 'integer',
        'est_obligatoire' => 'boolean',
    ];

    protected $appends = [
        'nom_complet',
    ];

    public function professeurs(): BelongsToMany
    {
        return $this->belongsToMany(Professeur::class, 'enseignements', 'matiere_id', 'professeur_id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'enseignements', 'matiere_id', 'classe_id');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'matiere_id');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function getNomCompletAttribute(): string
    {
        $nom = $this->nom;
        if ($this->niveau) {
            $nom = "{$this->niveau->nom} - {$nom}";
        }
        return $nom;
    }

    public function scopeObligatoires($query)
    {
        return $query->where('est_obligatoire', true);
    }

    public function scopePourNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }
}
