<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Enseignement extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'Enseignement';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IdProf',
        'IdFil',
        'IdMat',
        'IdNiv',
        'NbrEtu',
        'SalaireParEtu',
        'Somme',
        'Date_debut',



    ];

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
