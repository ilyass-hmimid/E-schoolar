<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Etudiant;

class Enseignement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'enseignements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'professeur_id',
        'matiere_id',
        'niveau_id',
        'filiere_id',
        'nombre_heures_semaine',
        'jour_cours',
        'heure_debut',
        'heure_fin',
        'est_actif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nombre_heures_semaine' => 'integer',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'est_actif' => 'boolean',
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

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    /**
     * Relation avec les élèves de la classe via la table etudiants
     */
    public function eleves()
    {
        return $this->hasManyThrough(
            User::class,
            Etudiant::class,
            'classe_id', // Clé étrangère dans la table etudiants
            'id',        // Clé primaire dans la table users
            'classe_id', // Clé étrangère dans la table enseignements
            'user_id'    // Clé étrangère dans la table etudiants qui fait référence à users
        )->where('role', 'eleve');
    }

    /**
     * Scopes
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeParProfesseur($query, $professeurId)
    {
        return $query->where('professeur_id', $professeurId);
    }

    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    public function scopeParNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    public function scopeParFiliere($query, $filiereId)
    {
        return $query->where('filiere_id', $filiereId);
    }

    public function scopeParJour($query, $jour)
    {
        return $query->where('jour_cours', $jour);
    }
}
