<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Professeur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'Professeurs';

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
        'Date_debut',
        'SommeApaye',

    ];


    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'Enseignement', 'IdProf', 'IdMat');
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'Enseignement', 'IdProf', 'IdFil');
    }

    public function niveaux()
    {
        return $this->belongsToMany(Niveau::class, 'Enseignement', 'IdProf', 'IdNiv');
    }


    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class, 'Inscription', 'IdProf', 'IdEtu');
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
