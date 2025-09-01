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
     * Relations
     */
    
    /**
     * Les professeurs qui enseignent cette matière
     */
    public function professeurs(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Enseignant::class, 'matiere_professeur', 'matiere_id', 'user_id')
            ->withTimestamps()
            ->withPivot([
                'created_at',
                'updated_at',
                'est_coordinateur' // Si un professeur est coordinateur de la matière
            ])
            ->wherePivot('est_actif', true); // Pour ne récupérer que les affectations actives
    }
    
    /**
     * Les niveaux dans lesquels cette matière est enseignée
     */
    public function niveaux(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Niveau::class, 'matiere_niveau')
            ->withTimestamps()
            ->withPivot([
                'coefficient',
                'volume_horaire',
                'est_obligatoire'
            ]);
    }
    
    /**
     * Les filières dans lesquelles cette matière est enseignée
     */
    public function filieres(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Filiere::class, 'filiere_matiere')
            ->withTimestamps()
            ->withPivot([
                'coefficient',
                'est_principale',
                'est_optionnelle'
            ]);
    }
    
    /**
     * Les classes dans lesquelles cette matière est enseignée
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Classe::class, 'enseignements', 'matiere_id', 'classe_id')
            ->withPivot([
                'professeur_id',
                'niveau_id',
                'filiere_id',
                'nombre_heures_semaine',
                'jour_cours',
                'heure_debut',
                'heure_fin',
                'salle',
                'est_actif'
            ])
            ->withTimestamps()
            ->wherePivot('est_actif', true) // Pour ne récupérer que les affectations actives
            ->distinct();
    }
    
    /**
     * Les enseignements associés à cette matière
     */
    public function enseignements(): HasMany
    {
        return $this->hasMany(\App\Models\Enseignement::class, 'matiere_id');
    }
    
    /**
     * Les notes attribuées pour cette matière
     */
    public function notes(): HasMany
    {
        return $this->hasMany(\App\Models\Note::class, 'matiere_id');
    }
    
    /**
     * Les absences enregistrées pour cette matière
     */
    public function absences(): HasMany
    {
        return $this->hasMany(\App\Models\Absence::class, 'matiere_id');
    }
    
    /**
     * Les étudiants inscrits à cette matière
     */
    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'inscriptions', 'matiere_id', 'etudiant_id')
            ->withPivot([
                'annee_scolaire',
                'niveau_id',
                'filiere_id',
                'date_inscription',
                'mode_paiement'
            ]);
    }
    
    /**
     * Méthodes utilitaires
     */
    
    /**
     * Vérifie si la matière est enseignée par un professeur donné
     */
    public function estEnseigneePar(int $professeurId): bool
    {
        return $this->professeurs()->where('user_id', $professeurId)->exists() ||
               $this->enseignements()->where('professeur_id', $professeurId)->exists();
    }
    
    /**
     * Obtient le coefficient de la matière selon le type
     */
    public function getCoefficientParDefaut(): int
    {
        return self::COEFFICIENTS[$this->type] ?? 1;
    }
    
    /**
     * Vérifie si la matière est obligatoire pour un niveau donné
     */
    public function estObligatoirePourNiveau(int $niveauId): bool
    {
        return $this->niveaux()
            ->where('niveau_id', $niveauId)
            ->wherePivot('est_obligatoire', true)
            ->exists();
    }
    
    /**
     * Calcule la moyenne de la matière pour un étudiant donné
     */
    public function calculerMoyenneEtudiant(int $etudiantId, string $trimestre = null): ?float
    {
        $query = $this->notes()
            ->where('etudiant_id', $etudiantId);
            
        if ($trimestre) {
            $query->where('trimestre', $trimestre);
        }
        
        $notes = $query->get();
        
        if ($notes->isEmpty()) {
            return null;
        }
        
        $totalPondere = $notes->sum(function ($note) {
            return $note->note * $note->coefficient;
        });
        
        $totalCoefficients = $notes->sum('coefficient');
        
        return $totalCoefficients > 0 ? round($totalPondere / $totalCoefficients, 2) : null;
    }
    /**
     * Les paiements associés à cette matière
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(\App\Models\Paiement::class, 'matiere_id');
    }
    
    /**
     * Les tarifs associés à cette matière
     */
    public function tarifs(): HasMany
    {
        return $this->hasMany(\App\Models\Tarif::class, 'matiere_id');
    }

    /**
     * Relation many-to-many avec les packs.
     */
    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Pack::class, 'matiere_pack')
            ->withPivot('nombre_heures_par_matiere');
    }
    
    /**
     * Relation avec les salaires des professeurs pour cette matière
     */
    public function salaires(): HasMany
    {
        return $this->hasMany(\App\Models\Salaire::class, 'matiere_id');
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
        return $this->etudiants()
            ->wherePivot('est_actif', true)
            ->count();
    }
}
