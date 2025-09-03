<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Enseignant;

class Professeur extends Enseignant
{
    // Utilise la table 'enseignants' du modèle parent

    /**
     * Relation avec les matières enseignées par le professeur
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignements', 'professeur_id', 'matiere_id');
    }

    /**
     * Relation avec les filières enseignées par le professeur
     */
    public function filieres(): BelongsToMany
    {
        return $this->belongsToMany(Filiere::class, 'enseignements', 'professeur_id', 'filiere_id');
    }

    /**
     * Relation avec les niveaux enseignés par le professeur
     */
    public function niveaux(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'enseignements', 'professeur_id', 'niveau_id');
    }

    /**
     * Relation avec les étudiants du professeur
     */
    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(Etudiant::class, 'inscriptions', 'professeur_id', 'etudiant_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];
}
