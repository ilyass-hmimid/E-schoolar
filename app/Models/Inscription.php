<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Inscription extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'Inscription';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IdEtu',
        'IdMat',
        'IdProf',
        'IdNiv',
        'IdFil',

    ];

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'Inscription', 'IdEtu', 'IdMat');
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
