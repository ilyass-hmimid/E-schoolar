<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Eleve extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'telephone',
        'sexe',
        'cin',
        'cne',
        'date_inscription',
        'nom_pere',
        'profession_pere',
        'telephone_pere',
        'nom_mere',
        'profession_mere',
        'telephone_mere',
        'adresse_parents',
        'remarques',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_inscription' => 'date',
    ];

    protected $appends = [
        'nom_complet',
        'age',
    ];

    /**
     * Relation avec l'utilisateur associé
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les absences de l'élève
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'eleve_id')
            ->orderBy('date_absence', 'desc');
    }

    /**
     * Relation avec les paiements de l'élève (nouvelle relation)
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'eleve_id');
    }

    /**
     * Relation avec les paiements de l'élève (ancienne relation)
     */
    public function anciensPaiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'etudiant_id', 'user_id');
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return $this->user ? $this->user->name : 'Utilisateur inconnu';
    }

    /**
     * Relation avec les matières de l'élève
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'eleve_matiere', 'eleve_id', 'matiere_id')
            ->withTimestamps();
    }

    /**
     * Accessor pour l'âge
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }
        
        return Carbon::parse($this->date_naissance)->age;
    }

    /**
     * Vérifie si l'élève est actif
     */
    public function estActif(): bool
    {
        return $this->user ? $this->user->is_active : false;
    }

    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }


    public function getStatutPaiementAttribute(): string
    {
        $dernierPaiement = $this->paiements()->latest('date_paiement')->first();
        
        if (!$dernierPaiement) {
            return 'jamais_paye';
        }
        
        return $dernierPaiement->statut;
    }
}
