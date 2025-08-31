<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarif extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pack_id',
        'niveau_id',
        'filiere_id',
        'montant',
        'type_tarif',
        'periode_validite_debut',
        'periode_validite_fin',
        'est_actif',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'est_actif' => 'boolean',
        'periode_validite_debut' => 'date',
        'periode_validite_fin' => 'date',
    ];

    /**
     * Les types de tarifs disponibles.
     *
     * @var array
     */
    public static $types = [
        'standard' => 'Standard',
        'promotionnel' => 'Promotionnel',
        'groupe' => 'Groupe',
        'premium' => 'Premium',
    ];

    /**
     * Relation avec le modèle Pack.
     */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    /**
     * Relation avec le modèle Niveau.
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Relation avec le modèle Filiere.
     */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    /**
     * Relation avec le modèle Paiement.
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Vérifie si le tarif est actif.
     *
     * @return bool
     */
    public function estActif(): bool
    {
        return $this->est_actif && 
               $this->periode_valide;
    }

    /**
     * Vérifie si la période de validité du tarif est en cours.
     *
     * @return bool
     */
    public function getPeriodeValideAttribute(): bool
    {
        $now = now();
        
        return ($this->periode_validite_debut === null || $this->periode_validite_debut->lte($now)) &&
               ($this->periode_validite_fin === null || $this->periode_validite_fin->gte($now));
    }

    /**
     * Scope pour les tarifs actifs.
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true)
                    ->where(function($q) {
                        $now = now()->format('Y-m-d');
                        $q->whereNull('periode_validite_debut')
                          ->orWhere('periode_validite_debut', '<=', $now);
                    })
                    ->where(function($q) {
                        $now = now()->format('Y-m-d');
                        $q->whereNull('periode_validite_fin')
                          ->orWhere('periode_validite_fin', '>=', $now);
                    });
    }

    /**
     * Scope pour les tarifs par pack.
     */
    public function scopeParPack($query, $packId)
    {
        return $query->where('pack_id', $packId);
    }

    /**
     * Scope pour les tarifs par niveau.
     */
    public function scopeParNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    /**
     * Scope pour les tarifs par filière.
     */
    public function scopeParFiliere($query, $filiereId)
    {
        return $query->where('filiere_id', $filiereId);
    }
}
