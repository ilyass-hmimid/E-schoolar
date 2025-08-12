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
        'pack_id',
        'heures_restantes',
        'date_expiration',
        'inscrit',
        'dateInscription'
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_expiration' => 'date',
        'dateInscription' => 'datetime',
        'heures_restantes' => 'integer',
        'inscrit' => 'boolean',
    ];

    /**
     * Relation avec le modèle Matiere.
     */
    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'IdMat');
    }

    /**
     * Relation avec le modèle Etudiant.
     */
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'IdEtu');
    }

    /**
     * Relation avec le modèle Professeur.
     */
    public function professeur()
    {
        return $this->belongsTo(Professeur::class, 'IdProf');
    }

    /**
     * Relation avec le modèle Niveau.
     */
    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'IdNiv');
    }

    /**
     * Relation avec le modèle Filiere.
     */
    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'IdFil');
    }

    /**
     * Relation avec le modèle Pack.
     */
    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }

    /**
     * Vérifie si l'inscription est associée à un pack.
     */
    public function aUnPack(): bool
    {
        return !is_null($this->pack_id);
    }

    /**
     * Vérifie si le pack est expiré.
     */
    public function estExpire(): bool
    {
        if (is_null($this->date_expiration)) {
            return false;
        }
        return now()->gt($this->date_expiration);
    }

    /**
     * Vérifie s'il reste des heures disponibles dans le pack.
     */
    public function aDesHeuresRestantes(): bool
    {
        return $this->heures_restantes > 0;
    }

    /**
     * Consomme un certain nombre d'heures du pack.
     */
    public function consommerHeures(int $nombreHeures): bool
    {
        if (!$this->aUnPack() || $this->estExpire() || $this->heures_restantes < $nombreHeures) {
            return false;
        }

        $this->decrement('heures_restantes', $nombreHeures);
        return true;
    }

    /**
     * Ajoute des heures supplémentaires au pack.
     */
    public function ajouterHeures(int $nombreHeures): void
    {
        if ($this->aUnPack()) {
            $this->increment('heures_restantes', $nombreHeures);
        }
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
