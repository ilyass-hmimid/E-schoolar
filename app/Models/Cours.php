<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cours extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'matiere_id',
        'enseignant_id',
        'date',
        'heure_debut',
        'heure_fin',
        'salle',
        'statut',
        'contenu',
        'devoirs',
        'notes',
        'est_valide',
        'valide_par',
        'valide_le',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'est_valide' => 'boolean',
        'valide_le' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'statut' => 'planifié',
        'est_valide' => false,
    ];

    /**
     * Get the matiere that owns the cours.
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Get the enseignant that owns the cours.
     */
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    /**
     * Get the user who validated the cours.
     */
    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Get the absences for the cours.
     */
    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}
