<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscription extends Model
{
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'niveau_id',
        'filiere_id',
        'annee_scolaire',
        'date_inscription',
        'statut',
    ];

    protected $casts = [
        'date_inscription' => 'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }
}
