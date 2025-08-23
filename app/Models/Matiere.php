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
        'nom',
        'code',
        'description',
        'coefficient',
        'nombre_heures',
        'prix_mensuel',
        'commission_prof',
        'est_actif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'coefficient' => 'integer',
        'nombre_heures' => 'integer',
        'prix_mensuel' => 'decimal:2',
        'commission_prof' => 'decimal:2',
        'est_actif' => 'boolean',
    ];

    /**
     * Relations
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enseignements', 'matiere_id', 'professeur_id')
            ->withPivot(['niveau_id', 'filiere_id', 'nombre_heures_semaine'])
            ->withTimestamps();
    }

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

    public function scopeParCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Méthodes utilitaires
     */
    public function getPrixFormattedAttribute(): string
    {
        return number_format($this->prix_mensuel, 2, ',', ' ') . ' DH';
    }

    public function getCommissionFormattedAttribute(): string
    {
        return $this->commission_prof . '%';
    }

    public function getNombreElevesAttribute(): int
    {
        return $this->enseignements()
            ->whereHas('professeur', function ($query) {
                $query->where('role', 2); // PROFESSEUR
            })
            ->count();
    }

    public function getChiffreAffairesMensuelAttribute(): float
    {
        return $this->paiements()
            ->valides()
            ->where('mois_periode', now()->format('Y-m'))
            ->sum('montant');
    }
}
