<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'specialite',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
