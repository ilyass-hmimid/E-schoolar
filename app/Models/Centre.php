<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Centre extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'adresse',
        'ville',
        'pays',
        'telephone',
        'email',
        'responsable_id',
        'logo',
        'description',
        'is_active',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Obtenir les classes du centre.
     */
    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    /**
     * Obtenir les utilisateurs du centre.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Obtenir le responsable du centre.
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Obtenir les enseignants du centre.
     */
    public function enseignants()
    {
        return $this->hasMany(User::class)->role('professeur');
    }

    /**
     * Obtenir les élèves du centre.
     */
    public function eleves()
    {
        return $this->hasMany(User::class)->role('eleve');
    }

    /**
     * Obtenir le chemin complet du logo.
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('img/default-centre.png');
    }
}
