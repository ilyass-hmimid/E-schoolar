<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Enseignant;
use App\Models\Enseignement;

class Classe extends Model
{
    use SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code_classe',
        'nom',
        'niveau',
        'annee_scolaire',
        'filiere_id',
        'professeur_principal_id',
        'effectif_max',
        'description',
        'est_actif',
    ];
    
    /**
     * Les accesseurs à ajouter à la sortie JSON.
     *
     * @var array
     */
    protected $appends = ['nom_complet'];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'effectif_max' => 'integer',
        'est_actif' => 'boolean',
    ];

    /**
     * Relation avec la filière
     */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    /**
     * Relation avec le professeur principal
     */
    public function professeurPrincipal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_principal_id');
    }

    /**
     * Relation avec les étudiants de la classe
     */
    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class);
    }

    /**
     * Relation avec les matières enseignées dans cette classe
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignements', 'classe_id', 'matiere_id')
            ->withPivot(['professeur_id', 'nombre_heures_semaine'])
            ->withTimestamps()
            ->distinct();
    }

    /**
     * Relation avec les professeurs qui enseignent dans cette classe
     */
    public function professeurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enseignements', 'classe_id', 'professeur_id')
            ->withPivot(['matiere_id', 'nombre_heures_semaine'])
            ->withTimestamps()
            ->distinct();
    }

    /**
     * Relation avec les présences de la classe
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Relation avec les enseignants de la classe
     */
    
    /**
     * Accesseur pour le nom complet de la classe
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        $nomComplet = "{$this->nom} - {$this->niveau}";
        
        if ($this->filiere) {
            $nomComplet .= " ({$this->filiere->nom})";
        }
        
        if ($this->annee_scolaire) {
            $nomComplet .= " - {$this->annee_scolaire}";
        }
        
        return $nomComplet;
    }
    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_classe', 'classe_id', 'enseignant_id')
            ->withPivot([
                'matiere',
                'est_principal',
                'date_debut',
                'date_fin',
                'commentaires'
            ]);
    }

    /**
     * Relation avec les enseignements de la classe
     */
    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class, 'classe_id');
    }

    /**
     * Obtenir le libellé complet de la classe
     */
    public function getLibelleCompletAttribute(): string
    {
        return "{$this->niveau} - {$this->nom}" . ($this->filiere ? " ({$this->filiere->nom})" : '');
    }

    /**
     * Obtenir l'effectif actuel de la classe
     */
    public function getEffectifActuelAttribute(): int
    {
        return $this->etudiants()->count();
    }

    /**
     * Vérifier si la classe est complète
     */
    public function getEstCompleteAttribute(): bool
    {
        return $this->effectif_actuel >= $this->effectif_max;
    }

    /**
     * Scope pour les classes actives
     */
    public function scopeActives($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope pour les classes d'une année scolaire donnée
     */
    public function scopeAnneeScolaire($query, $anneeScolaire)
    {
        return $query->where('annee_scolaire', $anneeScolaire);
    }

    /**
     * Scope pour les classes d'un niveau donné
     */
    public function scopeNiveau($query, $niveau)
    {
        return $query->where('niveau', $niveau);
    }

    /**
     * Scope pour les classes d'une filière donnée
     */
    public function scopeFiliere($query, $filiereId)
    {
        return $query->where('filiere_id', $filiereId);
    }
}
