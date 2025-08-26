<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    use HasFactory;

    protected $fillable = [
        'cle',
        'valeur',
        'type',
        'description',
    ];

    protected $casts = [
        'valeur' => 'json',
    ];

    /**
     * Récupère la valeur d'un paramètre
     */
    public static function getParametre($cle, $valeurParDefaut = null)
    {
        $parametre = static::where('cle', $cle)->first();
        return $parametre ? $parametre->valeur : $valeurParDefaut;
    }

    /**
     * Définit la valeur d'un paramètre
     */
    public static function setParametre($cle, $valeur, $type = 'texte', $description = null)
    {
        return static::updateOrCreate(
            ['cle' => $cle],
            [
                'valeur' => $valeur,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
