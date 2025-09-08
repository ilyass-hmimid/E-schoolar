<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Professeur extends Enseignant
{
    protected $table = 'professeurs';
    
    protected $fillable = [
        'pourcentage_remuneration',
        'date_embauche',
        'est_actif',
        // autres champs existants...
    ];
    
    protected $appends = [
        'nom_complet',
        'anciennete',
        'salaire_mensuel'
    ];
    
    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array
     */
    protected $attributes = [
        'pourcentage_remuneration' => 30, // 30% par défaut
        'est_actif' => true,
    ];

    /**
     * Relation avec les matières enseignées
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignements', 'professeur_id', 'matiere_id')
            ->withPivot('pourcentage_remuneration', 'date_debut', 'date_fin')
            ->withTimestamps()
            ->withCount('eleves'); // Charger le nombre d'élèves directement
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'professeur_id');
    }

    /**
     * Calcule le salaire mensuel du professeur
     * 
     * @param string $mois Le mois au format 'YYYY-MM'
     * @return array Détails du calcul du salaire
     */
    public function calculateSalaireMensuel($mois)
    {
        $salaireTotal = 0;
        $details = [];
        $dateDebut = Carbon::parse($mois . '-01')->startOfMonth();
        $dateFin = $dateDebut->copy()->endOfMonth();

        // Pour chaque matière enseignée par le professeur
        foreach ($this->matieres as $matiere) {
            // Récupérer le pourcentage spécifique pour cette matière ou utiliser le pourcentage par défaut
            $pourcentage = $matiere->pivot->pourcentage_remuneration ?? $this->pourcentage_remuneration;
            
            // Récupérer le nombre d'élèves inscrits à cette matière
            $nombreEleves = $matiere->eleves()->count();
            
            // Récupérer le montant payé par élève pour cette matière
            $montantParEleve = $matiere->getPrixMensuel(); // Supposons que cette méthode existe
            
            // Calculer le salaire pour cette matière
            $salaireMatiere = $nombreEleves * $montantParEleve * ($pourcentage / 100);
            
            $details[] = [
                'matiere' => $matiere->nom,
                'nombre_eleves' => $nombreEleves,
                'montant_par_eleve' => $montantParEleve,
                'pourcentage' => $pourcentage,
                'salaire_matiere' => $salaireMatiere
            ];
            
            $salaireTotal += $salaireMatiere;
        }

        return [
            'salaire_total' => $salaireTotal,
            'details' => $details,
            'mois' => $dateDebut->format('F Y'),
            'professeur' => $this->nom_complet,
            'pourcentage_global' => $this->pourcentage_remuneration
        ];
    }

    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function getAncienneteAttribute(): ?int
    {
        if (!$this->date_embauche) {
            return null;
        }
        
        return $this->date_embauche->diffInYears(now());
    }

    /**
     * Calcule le salaire mensuel du professeur
     * 
     * @return float
     */
    /**
     * Calcule le salaire mensuel du professeur
     * 
     * @param string|null $niveau Filtrer par niveau (primaire, college, lycee)
     * @param bool $actifSeulement Ne prendre en compte que les matières actives
     * @return float
     */
    public function getSalaireMensuelAttribute(?string $niveau = null, bool $actifSeulement = true): float
    {
        // Charger les matières avec le nombre d'élèves si ce n'est pas déjà fait
        $matieres = $this->matieres
            ->when($actifSeulement, function($query) {
                return $query->where('est_active', true);
            })
            ->filter(function($matiere) use ($niveau) {
                return $niveau ? $matiere->niveau === $niveau : true;
            });
        
        $total = $matieres->sum(function($matiere) {
            $nombreEleves = $matiere->eleves_count ?? $matiere->eleves()->count();
            $pourcentage = $matiere->pivot->pourcentage_remuneration ?? $this->pourcentage_remuneration;
            return ($matiere->prix_mensuel * $nombreEleves) * ($pourcentage / 100);
        });
        
        return round($total, 2);
    }
    
    /**
     * Obtenir la répartition du salaire par matière
     * 
     * @param bool $actifSeulement Ne prendre en compte que les matières actives
     * @return array
     */
    public function getRepartitionSalaireAttribute(bool $actifSeulement = true): array
    {
        $matieres = $actifSeulement 
            ? $this->matieres->where('est_active', true) 
            : $this->matieres;
            
        return $matieres->mapWithKeys(function($matiere) {
            $nombreEleves = $matiere->eleves_count ?? $matiere->eleves()->count();
            $pourcentage = $matiere->pivot->pourcentage_remuneration ?? $this->pourcentage_remuneration;
            $salaire = ($matiere->prix_mensuel * $nombreEleves) * ($pourcentage / 100);
            
            return [
                $matiere->id => [
                    'matiere_id' => $matiere->id,
                    'matiere_nom' => $matiere->nom,
                    'niveau' => $matiere->niveau,
                    'libelle_niveau' => $matiere->libelle_niveau,
                    'eleves' => $nombreEleves,
                    'prix' => $matiere->prix_mensuel,
                    'pourcentage' => $pourcentage,
                    'salaire' => round($salaire, 2),
                    'est_active' => $matiere->est_active,
                    'date_debut' => $matiere->pivot->date_debut ?? null,
                    'date_fin' => $matiere->pivot->date_fin ?? null
                ]
            ];
        })->sortBy('matiere_nom')->toArray();
    }

    /**
     * Relation avec les paiements reçus
     */
    public function paiements()
    {
        return $this->hasMany(PaiementProfesseur::class)
            ->orderBy('date_paiement', 'desc')
            ->orderBy('created_at', 'desc');
    }
    
    /**
     * Obtenir les paiements pour une période donnée
     * 
     * @param string $periode 'mois_courant', 'mois_precedent' ou une année complète '2023'
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiementsParPeriode(?string $periode = 'mois_courant')
    {
        $query = $this->paiements();
        
        if ($periode === 'mois_courant') {
            $debut = now()->startOfMonth();
            $fin = now()->endOfMonth();
            $query->whereBetween('date_paiement', [$debut, $fin]);
        } elseif ($periode === 'mois_precedent') {
            $debut = now()->subMonth()->startOfMonth();
            $fin = now()->subMonth()->endOfMonth();
            $query->whereBetween('date_paiement', [$debut, $fin]);
        } elseif (is_numeric($periode) && strlen($periode) === 4) {
            // Année complète
            $query->whereYear('date_paiement', $periode);
        }
        
        return $query;
    }
    
    /**
     * Obtenir les paiements du mois en cours (alias pour rétrocompatibilité)
     */
    public function paiementsMoisCourant()
    {
        return $this->paiementsParPeriode('mois_courant');
    }
    
    /**
     * Obtenir le solde total des paiements
     * 
     * @param string|null $periode Période pour filtrer les paiements (optionnel)
     * @return float
     */
    public function getSoldeTotalAttribute(?string $periode = null): float
    {
        if ($periode) {
            return (float) $this->paiementsParPeriode($periode)->sum('montant');
        }
        
        return (float) $this->paiements()->sum('montant');
    }
    
    /**
     * Obtenir le solde restant à payer pour une période
     * 
     * @param string $periode Période pour le calcul
     * @return float
     */
    public function getSoldeRestantAttribute(?string $periode = null): float
    {
        $salaire = $this->getSalaireMensuelAttribute();
        $paiements = $this->getSoldeTotalAttribute($periode);
        
        return max(0, $salaire - $paiements);
    }

    /**
     * Vérifie si le professeur est actif
     * 
     * @return bool
     */
    public function estActif(): bool
    {
        return (bool) $this->est_actif;
    }
    
    /**
     * Vérifie si le professeur enseigne une matière spécifique
     * 
     * @param int $matiereId ID de la matière
     * @return bool
     */
    public function enseigneMatiere(int $matiereId): bool
    {
        return $this->matieres()->where('matieres.id', $matiereId)->exists();
    }
    
    /**
     * Attribuer une matière au professeur
     * 
     * @param int $matiereId ID de la matière
     * @param array $donnees Données supplémentaires (pourcentage_remuneration, date_debut, etc.)
     * @return void
     */
    public function attribuerMatiere(int $matiereId, array $donnees = []): void
    {
        $this->matieres()->syncWithoutDetaching([
            $matiereId => array_merge([
                'pourcentage_remuneration' => $this->pourcentage_remuneration,
                'date_debut' => now(),
                'date_fin' => null,
            ], $donnees)
        ]);
    }
    
    /**
     * Retirer une matière au professeur
     * 
     * @param int $matiereId ID de la matière
     * @return int Nombre de relations supprimées
     */
    public function retirerMatiere(int $matiereId): int
    {
        return $this->matieres()->detach($matiereId);
    }
}
