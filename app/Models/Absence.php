<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Matiere;

class Absence extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'absences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'professeur_id',
        'assistant_id',
        'date_absence',
        'heure_debut',
        'heure_fin',
        'type',
        'duree_retard',
        'motif',
        'justifiee',
        'justification',
        'statut_justification',
        'piece_jointe',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_absence' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'duree_retard' => 'integer',
        'justifiee' => 'boolean',
        'statut_justification' => 'string',
    ];
    
    /**
     * Les valeurs possibles pour le statut de justification
     */
    public const STATUT_JUSTIFICATION = [
        'en_attente' => 'En attente',
        'validee' => 'Validée',
        'rejetee' => 'Rejetée',
    ];

    /**
     * Relations
     */
    /**
     * Relation avec l'étudiant concerné par l'absence
     */
    public function etudiant(): BelongsTo
    {
        // Essayer d'abord avec la table users, puis avec etudiants si nécessaire
        if (class_exists(Etudiant::class)) {
            return $this->belongsTo(Etudiant::class, 'etudiant_id');
        }
        
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    /**
     * Relation avec la matière concernée
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id')
            ->withDefault([
                'code' => 'NC',
                'nom' => 'Non défini'
            ]);
    }

    /**
     * Relation avec le professeur qui a signalé l'absence
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id')
            ->withDefault([
                'name' => 'Non défini',
                'email' => 'inconnu@exemple.com'
            ]);
    }

    /**
     * Relation avec l'assistant qui a enregistré l'absence
     */
    public function assistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistant_id')
            ->withDefault([
                'name' => 'Système',
                'email' => 'system@exemple.com'
            ]);
    }

    /**
     * Scopes
     */
    public function scopeAbsences($query)
    {
        return $query->where('type', 'absence');
    }

    public function scopeRetards($query)
    {
        return $query->where('type', 'retard');
    }

    public function scopeJustifiees($query)
    {
        return $query->where('justifiee', true);
    }
    
    /**
     * Vérifie si l'absence est justifiée
     */
    public function estJustifiee(): bool
    {
        return $this->justifiee && $this->statut_justification === 'validee';
    }
    
    /**
     * Vérifie si une justification est en attente de validation
     */
    public function estEnAttenteDeValidation(): bool
    {
        return $this->justification !== null && $this->statut_justification === 'en_attente';
    }
    
    /**
     * Marque l'absence comme justifiée
     */
    public function marquerCommeJustifiee(string $commentaire = null, int $validePar = null): void
    {
        $this->update([
            'justifiee' => true,
            'statut_justification' => 'validee',
            'commentaire_validation' => $commentaire,
            'valide_par' => $validePar ?? auth()->id(),
            'date_validation' => now(),
        ]);
    }

    public function scopeNonJustifiees($query)
    {
        return $query->where('justifiee', false);
    }

    /**
     * Scope pour filtrer les absences par date ou par plage de dates
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $startDate
     * @param string|\Carbon\Carbon|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParDate($query, $startDate, $endDate = null)
    {
        if ($endDate === null) {
            return $query->whereDate('date_absence', $startDate);
        }
        
        return $query->whereBetween('date_absence', [
            $startDate,
            $endDate
        ]);
    }

    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    public function scopeParEtudiant($query, $etudiantId)
    {
        return $query->where('etudiant_id', $etudiantId);
    }
    
    /**
     * Obtient la classe CSS pour le badge de statut de justification
     */
    public function getStatutJustificationBadgeClassAttribute(): string
    {
        return match($this->statut_justification) {
            'en_attente' => 'bg-yellow-100 text-yellow-800',
            'validee' => 'bg-green-100 text-green-800',
            'rejetee' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
    
    /**
     * Obtient le libellé du statut de justification
     */
    public function getStatutJustificationLabelAttribute(): string
    {
        return self::STATUT_JUSTIFICATION[$this->statut_justification] ?? 'Inconnu';
    }
    
    /**
     * Vérifie si l'absence peut être justifiée par l'étudiant
     */
    public function getPeutEtreJustifieeAttribute(): bool
    {
        return $this->type === 'absence' && 
               !$this->justifiee && 
               $this->date_absence >= now()->subDays(7) &&
               $this->statut_justification !== 'validee';
    }
    
    /**
     * Vérifie si l'absence est en attente de justification
     */
    public function getEstEnAttenteJustificationAttribute(): bool
    {
        return $this->statut_justification === 'en_attente';
    }
    
    /**
     * Vérifie si la justification de l'absence a été validée
     */
    public function getEstJustificationValideeAttribute(): bool
    {
        return $this->statut_justification === 'validee';
    }
    
    /**
     * Vérifie si la justification de l'absence a été rejetée
     */
    public function getEstJustificationRejeteeAttribute(): bool
    {
        return $this->statut_justification === 'rejetee';
    }
    
    /**
     * Accessor pour l'URL de la pièce jointe
     */
    public function getPieceJointeUrlAttribute(): ?string
    {
        return $this->piece_jointe ? asset('storage/' . $this->piece_jointe) : null;
    }
    
    /**
     * Vérifie si l'absence a une pièce jointe
     */
    public function getAPieceJointeAttribute(): bool
    {
        return !empty($this->piece_jointe);
    }
}
