<?php

namespace App\Models;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role',
        'phone',
        'address',
        'parent_phone',
        'parent_email',
        'niveau_id',
        'filiere_id',
        'somme_a_payer',
        'date_debut',
        'is_active',
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
        'password' => 'hashed',
        'role' => RoleType::class,
        'somme_a_payer' => 'decimal:2',
        'date_debut' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relations
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    // Relations pour les professeurs
    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class, 'professeur_id');
    }

    public function matieresEnseignees(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignements', 'professeur_id', 'matiere_id')
            ->withPivot(['niveau_id', 'filiere_id', 'nombre_heures_semaine'])
            ->withTimestamps();
    }

    public function salaires(): HasMany
    {
        return $this->hasMany(Salaire::class, 'professeur_id');
    }

    // Relations pour les élèves
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'etudiant_id');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'etudiant_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'etudiant_id');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'IdEtudiant');
    }

    // Relations pour les assistants
    public function absencesEnregistrees(): HasMany
    {
        return $this->hasMany(Absence::class, 'assistant_id');
    }

    public function paiementsValides(): HasMany
    {
        return $this->hasMany(Paiement::class, 'assistant_id');
    }

    /**
     * Scopes pour filtrer par rôle
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', RoleType::ADMIN);
    }

    public function scopeProfesseurs($query)
    {
        return $query->where('role', RoleType::PROFESSEUR);
    }

    public function scopeAssistants($query)
    {
        return $query->where('role', RoleType::ASSISTANT);
    }

    public function scopeEleves($query)
    {
        return $query->where('role', RoleType::ELEVE);
    }

    public function scopeParents($query)
    {
        return $query->where('role', RoleType::PARENT);
    }

    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Méthodes utilitaires
     */
    public function isAdmin(): bool
    {
        return $this->role === RoleType::ADMIN;
    }

    public function isProfesseur(): bool
    {
        return $this->role === RoleType::PROFESSEUR;
    }

    public function isAssistant(): bool
    {
        return $this->role === RoleType::ASSISTANT;
    }

    public function isEleve(): bool
    {
        return $this->role === RoleType::ELEVE;
    }

    public function isParent(): bool
    {
        return $this->role === RoleType::PARENT;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role->hasPermission($permission);
    }

    /**
     * Calculer le salaire du professeur pour une période donnée
     */
    public function calculerSalaire(string $moisPeriode): float
    {
        if (!$this->isProfesseur()) {
            return 0;
        }

        return $this->salaires()
            ->where('mois_periode', $moisPeriode)
            ->where('statut', 'en_attente')
            ->sum('montant_net');
    }

    /**
     * Obtenir le total des paiements d'un élève pour une période
     */
    public function totalPaiements(string $moisPeriode): float
    {
        if (!$this->isEleve()) {
            return 0;
        }

        return $this->paiements()
            ->where('mois_periode', $moisPeriode)
            ->where('statut', 'valide')
            ->sum('montant');
    }

    /**
     * Obtenir la moyenne d'un élève pour une matière
     */
    public function moyenneMatiere(int $matiereId, string $trimestre = null): float
    {
        if (!$this->isEleve()) {
            return 0;
        }

        $query = $this->notes()->where('matiere_id', $matiereId);
        
        if ($trimestre) {
            $query->where('trimestre', $trimestre);
        }

        $notes = $query->get();
        
        if ($notes->isEmpty()) {
            return 0;
        }

        $totalPondere = $notes->sum(function ($note) {
            return $note->note * $note->coefficient;
        });

        $totalCoefficients = $notes->sum('coefficient');

        return $totalCoefficients > 0 ? round($totalPondere / $totalCoefficients, 2) : 0;
    }
}
