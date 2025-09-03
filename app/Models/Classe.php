<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classe extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'nom',
        'niveau_id',
        'filiere_id',
        'annee_scolaire',
        'effectif_max',
        'description',
        'est_actif',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'effectif_max' => 'integer',
        'est_actif' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    /**
     * Get the devoirs for the classe.
     */
    public function devoirs()
    {
        return $this->hasMany(Devoir::class);
    }

    protected $appends = [
        'nom_complet',
        'effectif_actuel',
        'est_complete',
    ];

    /**
     * Get the level that owns the class.
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Get the filiere that owns the class.
     */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    /**
     * Get the students for the class.
     */
    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'classe_id');
    }

    /**
     * Get the absences for the class.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'classe_id');
    }

    /**
     * Get the teachers who teach this class.
     */
    public function professeurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enseignements', 'classe_id', 'professeur_id')
            ->withPivot([
                'matiere_id',
                'niveau_id',
                'filiere_id',
                'nombre_heures_semaine',
                'jour_cours',
                'heure_debut',
                'heure_fin',
                'est_actif'
            ])
            ->withTimestamps()
            ->wherePivot('est_actif', true);
    }

    /**
     * Get the subjects taught in this class.
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignements', 'classe_id', 'matiere_id')
            ->withPivot([
                'professeur_id',
                'niveau_id',
                'filiere_id',
                'nombre_heures_semaine',
                'jour_cours',
                'heure_debut',
                'heure_fin',
                'est_actif'
            ])
            ->withTimestamps()
            ->wherePivot('est_actif', true)
            ->distinct();
    }

    /**
     * Get the courses for this class.
     */
    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class, 'classe_id');
    }

    /**
     * Scope to get only active classes
     */
    public function scopeActive($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope for classes in a specific school year
     */
    public function scopeAnneeScolaire($query, $anneeScolaire)
    {
        return $query->where('annee_scolaire', $anneeScolaire);
    }

    /**
     * Get the full name of the class
     */
    public function getNomCompletAttribute(): string
    {
        $niveau = $this->niveau ? $this->niveau->nom : 'Niveau inconnu';
        $nomComplet = "{$this->nom} - {$niveau}";
        
        if ($this->filiere) {
            $nomComplet .= " ({$this->filiere->nom})";
        }
        
        if ($this->annee_scolaire) {
            $nomComplet .= " - {$this->annee_scolaire}";
        }
        
        return $nomComplet;
    }

    /**
     * Get the current number of students in the class
     */
    public function getEffectifActuelAttribute(): int
    {
        return $this->eleves()->count();
    }

    /**
     * Check if the class is full
     */
    public function getEstCompleteAttribute(): bool
    {
        return $this->effectif_actuel >= $this->effectif_max;
    }
}
