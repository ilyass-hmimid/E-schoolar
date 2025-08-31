<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cours extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'description',
        'duree',
        'prix',
        'niveau_id',
        'matiere_id',
        'professeur_id',
        'est_actif',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'est_actif' => 'boolean',
        'prix' => 'decimal:2',
    ];

    /**
     * Obtenez le niveau associé au cours.
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Obtenez la matière associée au cours.
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Obtenez le professeur qui donne le cours.
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }
}
