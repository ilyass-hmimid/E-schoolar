<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Etudiant;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'professeur_id',
        'type',
        'titre',
        'description',
        'note',
        'coefficient',
        'date_evaluation',
        'trimestre',
        'commentaires',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'note' => 'decimal:2',
        'coefficient' => 'decimal:1',
        'date_evaluation' => 'date',
    ];

    /**
     * Relations
     */
    /**
     * Relation avec l'étudiant concerné par la note
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
     * Relation avec la matière évaluée
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id')
            ->withDefault([
                'code' => 'NC',
                'nom' => 'Matière non définie',
                'coefficient' => 1
            ]);
    }

    /**
     * Relation avec le professeur qui a attribué la note
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id')
            ->withDefault([
                'name' => 'Professeur non défini',
                'email' => 'professeur@exemple.com'
            ]);
    }

    /**
     * Scopes
     */
    /**
     * Types de notes possibles
     */
    public const TYPES = [
        'devoir' => 'Devoir',
        'interrogation' => 'Interrogation',
        'examen' => 'Examen',
        'participation' => 'Participation',
        'projet' => 'Projet',
        'autre' => 'Autre',
    ];

    /**
     * Coefficients par défaut selon le type de note
     */
    public const COEFFICIENTS = [
        'devoir' => 1,
        'interrogation' => 2,
        'examen' => 4,
        'participation' => 0.5,
        'projet' => 3,
        'autre' => 1,
    ];

    /**
     * Applique un filtre sur le type de note
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Filtre les notes par trimestre
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $trimestre
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParTrimestre($query, $trimestre)
    {
        return $query->where('trimestre', $trimestre);
    }
    
    /**
     * Filtre les notes par étudiant
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $etudiantId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParEtudiant($query, $etudiantId)
    {
        return $query->where('etudiant_id', $etudiantId);
    }
    
    /**
     * Filtre les notes par matière
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $matiereId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }
    
    /**
     * Filtre les notes par professeur
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $professeurId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParProfesseur($query, $professeurId)
    {
        return $query->where('professeur_id', $professeurId);
    }
    
    /**
     * Vérifie si la note est éliminatoire (en dessous de la moyenne)
     * 
     * @param float $noteEliminatoire Seuil d'élimination (par défaut 5.0)
     * @return bool
     */
    public function estEliminatoire(float $noteEliminatoire = 5.0): bool
    {
        return $this->note < $noteEliminatoire;
    }
    
    /**
     * Méthodes utilitaires
     */
     
    /**
     * Obtient le libellé du type de note
     * 
     * @return string Le libellé du type de note
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'controle' => 'Contrôle',
            'devoir' => 'Devoir',
            'examen' => 'Examen',
            'composition' => 'Composition',
            default => 'Inconnu',
        };
    }

    /**
     * Obtient la note arrondie à deux décimales
     * 
     * @return float La note arrondie
     */
    public function getNoteSurVingtAttribute(): float
    {
        return round($this->note, 2);
    }

    /**
     * Calcule la note pondérée (note * coefficient)
     * 
     * @return float La note pondérée
     */
    public function getNotePondereeAttribute(): float
    {
        return round($this->note * $this->coefficient, 2);
    }

    /**
     * Récupère l'appréciation en fonction de la note
     * 
     * @return string L'appréciation correspondant à la note
     */
    public function getAppreciationAttribute(): string
    {
        if ($this->note >= 16) return 'Très bien';
        if ($this->note >= 14) return 'Bien';
        if ($this->note >= 12) return 'Assez bien';
        if ($this->note >= 10) return 'Passable';
        if ($this->note >= 8) return 'Insuffisant';
        return 'Très insuffisant';
    }

    /**
     * Obtient la classe CSS pour l'affichage de la note
     * 
     * @return string La classe CSS correspondant à la note
     */
    public function getAppreciationClassAttribute(): string
    {
        if ($this->note >= 16) return 'text-green-600';
        if ($this->note >= 14) return 'text-blue-600';
        if ($this->note >= 12) return 'text-yellow-600';
        if ($this->note >= 10) return 'text-orange-600';
        return 'text-red-600';
    }
}
