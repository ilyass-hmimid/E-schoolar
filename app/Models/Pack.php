<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pack extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'description',
        'nombre_heures',
        'prix',
        'est_actif',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'prix' => 'decimal:2',
        'est_actif' => 'boolean',
        'nombre_heures' => 'integer',
    ];

    /**
     * Les dates qui doivent être traitées comme des dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation many-to-many avec le modèle Matiere.
     */
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'matiere_pack')
            ->withPivot('nombre_heures_par_matiere')
            ->withTimestamps();
    }

    /**
     * Relation avec les inscriptions (étudiants qui ont souscrit à ce pack).
     */
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'pack_id');
    }

    /**
     * Scope pour les packs actifs.
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Vérifie si le pack est actif.
     */
    public function estActif(): bool
    {
        return $this->est_actif === true;
    }

    /**
     * Formate le prix avec le symbole de la devise.
     */
    public function getPrixFormateAttribute(): string
    {
        return number_format($this->prix, 2, ',', ' ') . ' MAD';
    }

    /**
     * Calcule le nombre total d'heures pour une matière spécifique dans ce pack.
     */
    public function getHeuresPourMatiere(int $matiereId): int
    {
        $matiere = $this->matieres()->where('matiere_id', $matiereId)->first();
        return $matiere ? (int) $matiere->pivot->nombre_heures_par_matiere : 0;
    }

    /**
     * Vérifie si le pack contient une matière spécifique.
     */
    public function contientMatiere(int $matiereId): bool
    {
        return $this->matieres()->where('matiere_id', $matiereId)->exists();
    }

    /**
     * Ajoute une matière au pack avec un nombre d'heures spécifié.
     */
    public function ajouterMatiere(int $matiereId, int $nombreHeures): void
    {
        $this->matieres()->syncWithoutDetaching([
            $matiereId => ['nombre_heures_par_matiere' => $nombreHeures]
        ]);
    }

    /**
     * Supprime une matière du pack.
     */
    public function supprimerMatiere(int $matiereId): void
    {
        $this->matieres()->detach($matiereId);
    }

    /**
     * Met à jour le nombre d'heures pour une matière spécifique dans le pack.
     */
    public function mettreAJourHeuresMatiere(int $matiereId, int $nombreHeures): void
    {
        $this->matieres()->updateExistingPivot($matiereId, [
            'nombre_heures_par_matiere' => $nombreHeures
        ]);
    }
}
