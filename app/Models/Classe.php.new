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
        'nom',
        'niveau',
        'description',
        'professeur_principal_id',
        'code',
        'niveau_id',
        'filiere_id',
        'annee_scolaire',
        'effectif_max',
        'est_actif',
        'created_by',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'professeur_principal_id' => 'integer',
        'effectif_max' => 'integer',
        'est_actif' => 'boolean',
        'niveau_id' => 'integer',
        'filiere_id' => 'integer',
        'created_by' => 'integer',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'eleves_count',
        'nom_complet',
        'effectif_actuel',
        'est_complete',
    ];
    
    /**
     * Get the eleves count attribute.
     *
     * @return int
     */
    public function getElevesCountAttribute()
    {
        return $this->eleves()->count();
    }

    /**
     * Get the professeur principal for the class.
     */
    public function professeurPrincipal()
    {
        return $this->belongsTo(User::class, 'professeur_principal_id');
    }
    
    /**
     * Get the eleves for the class.
     */
    public function eleves()
    {
        return $this->hasMany(User::class, 'classe_id')->where('role', 'eleve');
    }
    
    /**
     * Get the devoirs for the class.
     */
    public function devoirs()
    {
        return $this->hasMany(Devoir::class);
    }

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
        $niveau = $this->niveau ? $this->niveau->nom : $this->niveau;
        $nomComplet = "{$this->nom}";
        
        if ($niveau) {
            $nomComplet .= " - {$niveau}";
        }
        
        if ($this->filiere) {
            $nomComplet .= " ({$this->filiere->nom})";
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
        if (is_null($this->effectif_max)) {
            return false;
        }
        
        return $this->eleves_count >= $this->effectif_max;
    }
}
