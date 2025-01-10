<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Etudiant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'Etudiants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Nom',
        'Prenom',
        'Tele',
        'Adresse',
        'IdNiv',
        'IdFil',
        'SommeApaye',
        'Date_debut',

    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'IdNiv', 'id'); // `IdNiv` est la clé étrangère dans la table `etudiants`
    }

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'Inscription', 'IdEtu', 'IdMat')
            ->where('Inscription.inscrit', '=', true);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'IdEtu', 'id'); // `IdEtu` est la clé étrangère dans la table `Inscription`
    }

    public function professeurs()
    {
        return $this->belongsToMany(Professeur::class, 'Inscription', 'IdEtu', 'IdProf');
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
