<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etudiant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'etudiants';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'telephone2',
        'adresse',
        'date_naissance',
        'lieu_naissance',
        'classe_id',
        'filiere_id',
        'niveau_id',
        'date_inscription',
        'numero_etudiant',
        'nom_pere',
        'nom_mere',
        'telephone_pere',
        'telephone_mere',
        'adresse_parents',
        'photo',
        'est_actif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_naissance' => 'date',
        'date_inscription' => 'date',
        'est_actif' => 'boolean',
    ];

    /**
     * Relation avec la classe de l'étudiant
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec la filière de l'étudiant
     */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    /**
     * Relation avec le niveau de l'étudiant
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    /**
     * Relation avec les matières de l'étudiant
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'inscriptions', 'etudiant_id', 'matiere_id')
            ->withPivot(['note', 'appreciation', 'annee_scolaire'])
            ->withTimestamps();
    }

    /**
     * Relation avec les présences de l'étudiant
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'etudiant_id');
    }

    /**
     * Relation avec les absences de l'étudiant
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'etudiant_id');
    }

    /**
     * Relation avec les notes de l'étudiant
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'etudiant_id');
    }

    /**
     * Relation avec les paiements de l'étudiant
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'etudiant_id');
    }

    /**
     * Obtenir le nom complet de l'étudiant
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Vérifier si l'étudiant est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->est_actif;
    }

    /**
     * Scope pour les étudiants actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope pour les étudiants d'une classe donnée
     */
    public function scopeDeLaClasse($query, $classeId)
    {
        return $query->where('classe_id', $classeId);
    }

    /**
     * Scope pour les étudiants d'une filière donnée
     */
    public function scopeDeLaFiliere($query, $filiereId)
    {
        return $query->where('filiere_id', $filiereId);
    }

    /**
     * Scope pour les étudiants d'un niveau donné
     */
    public function scopeDuNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    /**
     * Relation avec les inscriptions de l'étudiant
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'etudiant_id');
    }


}
