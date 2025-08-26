<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'salaires';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'professeur_id',
        'matiere_id',
        'mois_periode',
        'nombre_eleves',
        'prix_unitaire',
        'commission_prof',
        'montant_brut',
        'montant_commission',
        'montant_net',
        'statut',
        'date_paiement',
        'commentaires',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nombre_eleves' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'commission_prof' => 'decimal:2',
        'montant_brut' => 'decimal:2',
        'montant_commission' => 'decimal:2',
        'montant_net' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    /**
     * Relations
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Scopes
     */
    public function scopePayes($query)
    {
        return $query->where('statut', 'paye');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeAnnules($query)
    {
        return $query->where('statut', 'annule');
    }

    public function scopeParMois($query, $moisPeriode)
    {
        return $query->where('mois_periode', $moisPeriode);
    }

    public function scopeParProfesseur($query, $professeurId)
    {
        return $query->where('professeur_id', $professeurId);
    }

    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    /**
     * Méthodes utilitaires
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'paye' => 'Payé',
            'annule' => 'Annulé',
            default => 'Inconnu',
        };
    }

    public function getStatutBadgeClassAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'bg-yellow-100 text-yellow-800',
            'paye' => 'bg-green-100 text-green-800',
            'annule' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Calculer automatiquement les montants
     */
    public function calculerMontants(): void
    {
        $this->montant_brut = $this->nombre_eleves * $this->prix_unitaire;
        $this->montant_commission = $this->montant_brut * ($this->commission_prof / 100);
        $this->montant_net = $this->montant_brut - $this->montant_commission;
    }

    /**
     * Boot method pour calculer automatiquement les montants
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($salaire) {
            $salaire->calculerMontants();
        });
    }
}
