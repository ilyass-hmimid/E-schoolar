<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Matiere extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'Matiere';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'Libelle',


    ];

    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class, 'Inscription', 'IdMat', 'IdEtu');
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

}
