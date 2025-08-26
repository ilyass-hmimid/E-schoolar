<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'etudiant_id',
        'professeur_id',
        'matiere_id',
        'classe_id',
        'date_seance',
        'heure_debut',
        'heure_fin',
        'statut',
        'duree_retard',
        'justificatif',
        'commentaire',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_seance' => 'date',
        'duree_retard' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les attributs qui doivent être ajoutés à la sortie du modèle.
     *
     * @var array
     */
    protected $appends = ['statut_libelle'];

    /**
     * Obtenir le libellé du statut.
     */
    public function getStatutLibelleAttribute(): string
    {
        return [
            'present' => 'Présent',
            'absent' => 'Absent',
            'retard' => 'Retard',
            'justifie' => 'Absence justifiée',
        ][$this->statut] ?? $this->statut;
    }

    /**
     * Obtenir l'étudiant associé à la présence.
     */
    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Obtenir le professeur associé à la présence.
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    /**
     * Obtenir la matière associée à la présence.
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Obtenir la classe associée à la présence.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Obtenir la politique d'accès pour le modèle.
     *
     * @return string
     */
    protected static function booted()
    {
        static::saving(function ($presence) {
            // S'assurer que la durée de retard est nulle si le statut n'est pas 'retard'
            if ($presence->statut !== 'retard') {
                $presence->duree_retard = null;
            }
            
            // S'assurer que le justificatif est nul si le statut n'est pas 'justifie'
            if ($presence->statut !== 'justifie') {
                $presence->justificatif = null;
            }
        });
    }

    /**
     * Obtenir la politique d'accès pour le modèle.
     *
     * @return string
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
    }
}
