<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Niveau extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'niveaux';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'code',
        'description',
        'type',
        'ordre',
        'est_actif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ordre' => 'integer',
        'est_actif' => 'boolean',
    ];

    /**
     * Relations
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class);
    }

    /**
     * Les filières associées à ce niveau
     */
    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }

    /**
     * Les matières associées à ce niveau
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'matiere_niveau')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre')->orderBy('nom');
    }
}
