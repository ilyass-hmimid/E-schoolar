<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Enseignant extends Model
{
    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'specialite',
        'diplome',
        'date_embauche',
        'statut',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_embauche' => 'date',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les cours donnés par l'enseignant
     */
    public function cours()
    {
        return $this->hasMany(Cours::class, 'professeur_id', 'user_id');
    }

    /**
     * Relation avec les notes attribuées par l'enseignant
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'professeur_id', 'user_id');
    }

    /**
     * Relation avec les matières enseignées
     */
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'matiere_professeur', 'professeur_id', 'matiere_id')
            ->withTimestamps();
    }

    /**
     * Relation avec les classes où l'enseignant donne cours
     */
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'enseignant_classe', 'enseignant_id', 'classe_id')
            ->withPivot([
                'matiere',
                'est_principal',
                'date_debut',
                'date_fin',
                'commentaires'
            ]);
    }

    /**
     * Relation avec les enseignements de l'enseignant
     */
    public function enseignements()
    {
        return $this->hasMany(Enseignement::class, 'professeur_id', 'user_id');
    }

    /**
     * Scope pour les enseignants actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Obtenir le nom complet de l'enseignant
     */
    public function getNomCompletAttribute(): string
    {
        return $this->user->prenom . ' ' . $this->user->nom;
    }

    /**
     * Obtenir l'email de l'enseignant
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user->email;
    }
}
