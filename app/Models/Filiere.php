<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Filiere extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'filieres';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'nom',
        'abreviation',
        'description',
        'niveau_id',
        'est_actif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'niveau_id' => 'integer',
        'est_actif' => 'boolean',
    ];

    /**
     * Relations
     */
    
    /**
     * Les utilisateurs (élèves) de cette filière
     */
    public function eleves(): HasMany
    {
        return $this->hasMany(User::class, 'filiere_id')
            ->where('role', 'etudiant');
    }
    
    /**
     * Le niveau auquel appartient cette filière
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }
    
    /**
     * Les matières enseignées dans cette filière
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'filiere_matiere')
            ->withTimestamps()
            ->withPivot(['created_at', 'updated_at']);
    }

    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'IdFil');
    }

    /**
     * Scopes
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeParCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Méthodes utilitaires
     */
    public function getFraisInscriptionFormattedAttribute(): string
    {
        return number_format($this->frais_inscription, 2, ',', ' ') . ' DH';
    }

    public function getFraisMensuelFormattedAttribute(): string
    {
        return number_format($this->frais_mensuel, 2, ',', ' ') . ' DH';
    }

    public function getNombreElevesAttribute(): int
    {
        return $this->users()->eleves()->count();
    }

    public function getNombreProfesseursAttribute(): int
    {
        return $this->enseignements()
            ->whereHas('professeur', function ($query) {
                $query->where('role', 2); // PROFESSEUR
            })
            ->distinct('professeur_id')
            ->count();
    }
}
