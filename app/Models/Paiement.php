<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Pack;
use App\Models\Tarif;
use App\Models\Etudiant;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paiements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'pack_id',
        'tarif_id',
        'assistant_id',
        'montant',
        'mode_paiement',
        'reference_paiement',
        'date_paiement',
        'statut',
        'commentaires',
        'mois_periode',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    /**
     * Relations
     */
    /**
     * Relation avec l'étudiant concerné par le paiement
     */
    public function etudiant(): BelongsTo
    {
        // Essayer d'abord avec la table users, puis avec etudiants si nécessaire
        if (class_exists(Etudiant::class)) {
            return $this->belongsTo(Etudiant::class, 'etudiant_id')
                ->withDefault([
                    'nom' => 'Inconnu',
                    'prenom' => 'Étudiant',
                    'email' => 'inconnu@exemple.com'
                ]);
        }
        
        return $this->belongsTo(User::class, 'etudiant_id')
            ->withDefault([
                'name' => 'Inconnu',
                'email' => 'inconnu@exemple.com'
            ]);
    }
    
    /**
     * Relation avec l'utilisateur (étudiant) via la relation etudiant
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Etudiant::class,
            'id', // Foreign key on etudiants table
            'id', // Foreign key on users table
            'etudiant_id', // Local key on paiements table
            'user_id' // Local key on etudiants table
        );
    }

    /**
     * Relation avec la matière concernée (si le paiement est lié à une matière spécifique)
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id')
            ->withDefault([
                'code' => 'NC',
                'nom' => 'Non spécifiée',
                'prix' => 0
            ]);
    }

    /**
     * Relation avec le pack (si le paiement est lié à un pack)
     */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class, 'pack_id')
            ->withDefault([
                'nom' => 'Aucun pack',
                'description' => 'Paiement hors pack',
                'prix' => 0
            ]);
    }

    /**
     * Relation avec le modèle Tarif.
     */
    /**
     * Relation avec le tarif appliqué
     */
    public function tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class, 'tarif_id')
            ->withDefault([
                'montant' => $this->montant ?? 0,
                'description' => 'Tarif par défaut'
            ]);
    }

    /**
     * Relation avec l'assistant qui a enregistré le paiement
     */
    public function assistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistant_id')
            ->withDefault([
                'name' => 'Système',
                'email' => 'system@allotawjih.ma',
                'role' => 'system'
            ]);
    }
    
    /**
     * Les factures associées à ce paiement
     */
    /**
     * Les factures associées à ce paiement
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class, 'paiement_id')
            ->orderBy('date_facture', 'desc');
    }
    
    /**
     * Les remboursements liés à ce paiement
     */
    /**
     * Les remboursements liés à ce paiement
     */
    public function remboursements(): HasMany
    {
        return $this->hasMany(Remboursement::class, 'paiement_id')
            ->orderBy('date_remboursement', 'desc');
    }
    
    /**
     * Calcule le montant total des remboursements
     */
    public function getTotalRembourseAttribute(): float
    {
        return (float) $this->remboursements()->sum('montant');
    }
    
    /**
     * Calcule le solde restant après déduction des remboursements
     */
    public function getSoldeRestantAttribute(): float
    {
        return (float) $this->montant - $this->total_rembourse;
    }
    
    /**
     * Vérifie si le paiement est complètement remboursé
     */
    public function estRembourseIntegralement(): bool
    {
        return $this->solde_restant <= 0.01; // Tolérance aux erreurs d'arrondi
    }
    
    /**
     * Statuts de paiement possibles
     */
    public const STATUTS = [
        'en_attente' => 'En attente',
        'valide' => 'Validé',
        'refuse' => 'Refusé',
        'rembourse' => 'Remboursé',
        'annule' => 'Annulé',
    ];
    
    /**
     * Modes de paiement possibles
     */
    public const MODES_PAIEMENT = [
        'especes' => 'Espèces',
        'cheque' => 'Chèque',
        'virement' => 'Virement bancaire',
        'carte' => 'Carte bancaire',
        'autre' => 'Autre',
    ];

    /**
     * Scopes
     */
    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
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

    public function scopeParEtudiant($query, $etudiantId)
    {
        return $query->where('etudiant_id', $etudiantId);
    }

    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    public function scopeParModePaiement($query, $mode)
    {
        return $query->where('mode_paiement', $mode);
    }

    /**
     * Scope pour filtrer les paiements par date ou par plage de dates
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $startDate
     * @param string|\Carbon\Carbon|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParDate($query, $startDate, $endDate = null)
    {
        if ($endDate === null) {
            return $query->whereDate('date_paiement', $startDate);
        }
        
        return $query->whereBetween('date_paiement', [
            $startDate,
            $endDate
        ]);
    }

    /**
     * Méthodes utilitaires
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'valide' => 'Validé',
            'annule' => 'Annulé',
            default => 'Inconnu',
        };
    }

    public function getModePaiementLabelAttribute(): string
    {
        return match($this->mode_paiement) {
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire',
            default => 'Inconnu',
        };
    }

    public function getStatutBadgeClassAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'bg-yellow-100 text-yellow-800',
            'valide' => 'bg-green-100 text-green-800',
            'annule' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
    
    /**
     * Vérifie si le paiement est éligible au remboursement
     */
    public function estRemboursable(): bool
    {
        // Un paiement est remboursable s'il est validé
        // et qu'il n'a pas déjà été intégralement remboursé
        return $this->statut === 'valide' && 
               $this->montant > $this->montantDejaRembourse();
    }
    
    /**
     * Calcule le montant déjà remboursé
     */
    public function montantDejaRembourse(): float
    {
        return (float) $this->remboursements()
            ->where('statut', 'valide')
            ->sum('montant');
    }
    
    /**
     * Calcule le montant restant à rembourser
     */
    public function montantRestantARembourser(): float
    {
        return max(0, $this->montant - $this->montantDejaRembourse());
    }
    
    /**
     * Vérifie si le paiement est en retard
     */
    public function estEnRetard(): bool
    {
        if ($this->statut !== 'en_attente') {
            return false;
        }
        
        // Un paiement est en retard s'il est en attente depuis plus de 3 jours
        return $this->created_at->diffInDays(now()) > 3;
    }
}
