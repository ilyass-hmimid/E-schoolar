<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matiere extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matieres';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'nom',
        'type',
        'description',
        'prix',
        'prix_prof',
        'est_actif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'string',
        'prix' => 'decimal:2',
        'prix_prof' => 'decimal:2',
        'est_actif' => 'boolean',
    ];
    
    /**
     * Les types de matières disponibles
     */
    public static $types = [
        'scientifique' => 'Scientifique',
        'litteraire' => 'Littéraire',
        'technique' => 'Technique',
        'langue' => 'Langue',
        'informatique' => 'Informatique',
        'autre' => 'Autre',
    ];

    /**
     * Relations
     */
    
    /**
     * Les professeurs qui enseignent cette matière
     */
    public function professeurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'matiere_professeur', 'matiere_id', 'user_id')
            ->withTimestamps()
            ->withPivot(['created_at', 'updated_at']);
    }
    
    /**
     * Les niveaux dans lesquels cette matière est enseignée
     */
    public function niveaux(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'matiere_niveau')
            ->withTimestamps();
    }
    
    /**
     * Les filières dans lesquelles cette matière est enseignée
     */
    public function filieres(): BelongsToMany
    {
        return $this->belongsToMany(Filiere::class, 'filiere_matiere')
            ->withTimestamps();
    }
    
    /**
     * Les classes dans lesquelles cette matière est enseignée
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'enseignements', 'matiere_id', 'classe_id')
            ->withPivot(['professeur_id', 'nombre_heures_semaine'])
            ->withTimestamps()
            ->distinct();
    }
    
    /**
     * Les enseignements associés à cette matière
     */
    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function salaires(): HasMany
    {
        return $this->hasMany(Salaire::class);
    }

    /**
     * Relation many-to-many avec les packs.
     */
    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(Pack::class, 'matiere_pack')
            ->withPivot('nombre_heures_par_matiere')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopePourNiveau($query, $niveauId)
    {
        return $query->whereHas('niveaux', function($q) use ($niveauId) {
            $q->where('niveaux.id', $niveauId);
        });
    }

    /**
     * Méthodes utilitaires
     */
    public function getPrixFormateAttribute(): string
    {
        return number_format($this->prix, 2, ',', ' ') . ' DH';
    }

    public function getPrixProfFormateAttribute(): string
    {
        return number_format($this->prix_prof, 2, ',', ' ') . ' DH';
    }

    public function getStatutLibelleAttribute(): string
    {
        return $this->est_actif ? 'Actif' : 'Inactif';
    }

    public function getNombreElevesAttribute(): int
    {
        return $this->enseignements()
            ->whereHas('professeur', function ($query) {
                $query->where('role', 2); // PROFESSEUR
            })
            ->count();
    }
}
