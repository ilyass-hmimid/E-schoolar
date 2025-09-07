<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matiere extends Model
{
    /**
     * Les matières fixes du système
     */
    const MATHS = 'Mathématiques';
    const SVT = 'SVT';
    const PHYSIQUE = 'Physique';
    const COMMUNICATION_FR = 'Communication Française';
    const COMMUNICATION_ANG = 'Communication Anglaise';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'description',
        'prix_mensuel',  // Prix mensuel par élève pour cette matière
        'couleur',       // Pour l'affichage dans le calendrier/interface
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'prix_mensuel' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec les élèves inscrits à cette matière
     */
    public function eleves(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'eleve_matiere', 'matiere_id', 'eleve_id')
            ->where('role', User::ROLE_ELEVE)
            ->withTimestamps();
    }

    /**
     * Relation avec les professeurs qui enseignent cette matière
     */
    public function professeurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'professeur_matiere', 'matiere_id', 'professeur_id')
            ->where('role', User::ROLE_PROFESSEUR)
            ->withPivot('est_responsable')
            ->withTimestamps();
    }

    /**
     * Relation avec les absences pour cette matière
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Obtenir le professeur responsable de cette matière
     */
    public function getProfesseurResponsableAttribute()
    {
        return $this->professeurs()
            ->wherePivot('est_responsable', true)
            ->first();
    }

    /**
     * Obtenir le nombre d'élèves inscrits à cette matière
     */
    public function getNombreElevesAttribute(): int
    {
        return $this->eleves()->count();
    }

    /**
     * Obtenir le chiffre d'affaires mensuel pour cette matière
     */
    public function getChiffreAffairesMensuelAttribute(): float
    {
        return $this->nombre_eleves * $this->prix_mensuel;
    }

    /**
     * Obtenir la liste des matières fixes
     */
    public static function getMatieresFixes(): array
    {
        return [
            self::MATHS,
            self::SVT,
            self::PHYSIQUE,
            self::COMMUNICATION_FR,
            self::COMMUNICATION_ANG,
        ];
    }

    /**
     * Vérifie si la matière est une matière fixe
     */
    public function isMatiereFixe(): bool
    {
        return in_array($this->nom, self::getMatieresFixes());
    }
}
