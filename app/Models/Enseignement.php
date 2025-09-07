<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enseignement extends Model
{
    protected $fillable = [
        'professeur_id',
        'matiere_id',
        'niveau_id',
        'filiere_id',
        'nombre_heures_semaine',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professeur()
    {
        return $this->belongsTo(User::class, 'professeur_id');
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
