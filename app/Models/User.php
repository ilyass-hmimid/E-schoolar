<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;
    
    const ROLE_ADMIN = 'admin';
    const ROLE_PROFESSEUR = 'professeur';
    const ROLE_ELEVE = 'eleve';

    const STATUS_ACTIF = 'actif';
    const STATUS_INACTIF = 'inactif';
    const STATUS_SUSPENDU = 'suspendu';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'date_naissance',
        'adresse',
        'telephone',
        'pourcentage_remuneration', // Pour les professeurs uniquement
        'date_embauche',           // Pour les professeurs
        'cne',                     // Pour les élèves
        'nom_pere',                // Pour les élèves
        'telephone_pere',          // Pour les élèves
        'nom_mere',                // Pour les élèves
        'telephone_mere'           // Pour les élèves
    ];
    
    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_naissance' => 'date',
        'date_embauche' => 'date',
        'pourcentage_remuneration' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array
     */
    protected $attributes = [
        'role' => self::ROLE_ELEVE,
        'status' => self::STATUS_ACTIF,
        'pourcentage_remuneration' => 30.00, // Valeur par défaut pour les professeurs
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
     * Vérifie si l'utilisateur est un administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur est un professeur
     */
    public function isProfesseur(): bool
    {
        return $this->role === self::ROLE_PROFESSEUR;
    }

    /**
     * Vérifie si l'utilisateur est un élève
     */
    public function isEleve(): bool
    {
        return $this->role === self::ROLE_ELEVE;
    }

    /**
     * Relation avec les matières de l'élève
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'eleve_matiere')
            ->withTimestamps();
    }

    /**
     * Relation avec les matières enseignées (pour les professeurs)
     */
    public function matieresEnseignees(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'professeur_matiere')
            ->withPivot('est_responsable')
            ->withTimestamps();
    }

    /**
     * Relation avec les absences (pour les élèves)
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'eleve_id');
    }

    /**
     * Relation avec les paiements (pour les élèves)
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'eleve_id');
    }

    /**
     * Relation avec les paiements (pour les professeurs)
     */
    public function salaires(): HasMany
    {
        return $this->hasMany(Salaire::class, 'professeur_id');
    }

    /**
     * Get the user's role.
     *
     * @return string
     */
    public function role()
    {
        return $this->attributes['role'] ?? null;
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     *
     * @param string|array $role
     * @return bool
     */
    /**
     * Vérifie si l'utilisateur a le rôle spécifié
     *
     * @param string|array $role Rôle ou tableau de rôles à vérifier
     * @return bool
     */
    public function hasRole($role): bool
    {
        // Si l'utilisateur est admin, il a automatiquement tous les rôles
        if ($this->is_admin === true) {
            return true;
        }
        
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        
        // Vérifier si le rôle demandé correspond au rôle de l'utilisateur
        return $this->role === $role;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'role_label'
    ];
    
    
    /**
     * Get the classe that the user belongs to.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }
    
    /**
     * Get the classes where this user is the main teacher.
     */
    public function classesEnseignees(): HasMany
    {
        return $this->hasMany(Classe::class, 'professeur_principal_id');
    }
    
    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\Models\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
    
    /**
     * Get the payments made by this user (for admin users who record payments)
     */
    public function paiementsEnregistres()
    {
        return $this->hasMany(PaiementProfesseur::class, 'enregistre_par');
    }
    
    /**
     * Get the payments for this user
     * - For teachers: payments received
     * - For students: payments made
     */
    public function paiements()
    {
        if ($this->hasRole('professeur')) {
            return $this->hasMany(PaiementProfesseur::class, 'professeur_id')
                ->orderBy('mois', 'desc');
        }
        
        if ($this->hasRole('eleve')) {
            // Vérifier si le modèle Paiement existe, sinon utiliser Paiment
            if (class_exists(Paiement::class)) {
                return $this->hasMany(Paiement::class, 'etudiant_id')
                    ->orderBy('date_paiement', 'desc');
            }
            
            return $this->hasMany(Paiment::class, 'etudiant_id')
                ->orderBy('date_paiement', 'desc');
        }
        
        // Par défaut, retourner une relation vide
        return $this->hasMany(Paiement::class, 'id', 'id')->whereRaw('1=0');
    }
    
    /**
     * Get the active payments for this teacher
     */
    public function paiementsActifs()
    {
        return $this->paiements()->where('statut', 'paye');
    }
    
    /**
     * Get the pending payments for this teacher
     */
    public function paiementsEnAttente()
    {
        return $this->paiements()->whereIn('statut', ['en_retard', 'impaye']);
    }
    
    /**
     * Get the full name of the user
     */
    public function getNomCompletAttribute()
    {
        return trim("{$this->prenom} {$this->nom}");
    }
    
    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    
    /**
     * Get the monthly salary for a specific month
     */
    public function getSalaireMensuel($month)
    {
        return $this->paiements()
            ->where('mois', $month)
            ->first();
    }
    
    /**
     * Check if the teacher has been paid for a specific month
     */
    public function estPayePourMois($month)
    {
        return $this->paiements()
            ->where('mois', $month)
            ->where('statut', 'paye')
            ->exists();
    }
    
    /**
     * Get the total amount paid to the teacher in a date range
     */
    public function getTotalPaiements($startDate = null, $endDate = null)
    {
        $query = $this->paiementsActifs();
        
        if ($startDate) {
            $query->whereDate('date_paiement', '>=', Carbon::parse($startDate));
        }
        
        if ($endDate) {
            $query->whereDate('date_paiement', '<=', Carbon::parse($endDate));
        }
        
        return $query->sum('montant');
    }
    
    /**
     * Get the channels the user receives notification broadcasts on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    
    /**
     * Get the human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            RoleType::ADMIN->value => 'Administrateur',
            RoleType::PROFESSEUR->value => 'Professeur',
            RoleType::ASSISTANT->value => 'Assistant',
            RoleType::ELEVE->value => 'Élève',
            default => 'Inconnu',
        };
    }

    
    /**
     * Obtenir le rôle de l'utilisateur sous forme d'énum RoleType
     * 
     * @param mixed $value
     * @return \App\Enums\RoleType
     */
    public function getRoleAttribute($value): RoleType
    {
        if ($value instanceof RoleType) {
            return $value;
        }
        
        if (is_numeric($value)) {
            return RoleType::from((int)$value);
        }
        
        if (is_string($value)) {
            $value = strtolower(trim($value));
            
            return match($value) {
                'admin', 'administrateur', '1' => RoleType::ADMIN,
                'professeur', 'prof', 'teacher', '2' => RoleType::PROFESSEUR,
                'assistant', 'assist', '3' => RoleType::ASSISTANT,
                'eleve', 'etudiant', 'student', '4' => RoleType::ELEVE,
                default => RoleType::ELEVE, // Par défaut, on considère que c'est un élève
            };
        }
        
        return RoleType::ELEVE; // Valeur par défaut si le type n'est pas reconnu
    }
    
    /**
     * Met à jour la date de dernière connexion
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
    
    
    /**
     * ============================================
     * SCOPES
     * ============================================
     */
    
    /**
     * Scope pour les administrateurs
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', RoleType::ADMIN);
    }

    /**
     * Scope pour les professeurs
     */
    public function scopeProfesseurs($query)
    {
        return $query->role('professeur');
    }

    /**
     * Scope pour les assistants
     */
    public function scopeAssistants($query)
    {
        return $query->where('role', RoleType::ASSISTANT);
    }

    /**
     * Scope pour les élèves
     */
    public function scopeEleves($query)
    {
        return $query->where('role', RoleType::ELEVE);
    }

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ============================================
     * RELATIONS
     * ============================================
     */
    
    /**
     * Obtenir le niveau de l'utilisateur (pour les élèves)
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Obtenir la filière de l'utilisateur (pour les élèves)
     */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    // Relations pour les professeurs
    
    /**
     * Obtenir les enseignements du professeur
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enseignements()
    {
        return $this->hasMany(\App\Models\Enseignement::class, 'professeur_id');
    }

    /**
     * Obtenir les matières enseignées par le professeur
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function matieresEnseignees()
    {
        return $this->belongsToMany(Matiere::class, 'enseignements', 'professeur_id', 'matiere_id')
            ->withPivot(['niveau_id', 'filiere_id', 'nombre_heures_semaine'])
            ->withTimestamps();
    }
    
    /**
     * Récupérer les classes associées au professeur via les enseignements
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'enseignements', 'professeur_id', 'classe_id')
            ->withPivot(['matiere_id', 'nombre_heures_semaine'])
            ->withTimestamps()
            ->distinct();
    }

    // Relations pour les élèves
    
    /**
     * Relation avec le modèle Etudiant (si l'utilisateur est un élève)
     */
    public function eleve()
    {
        return $this->hasOne(Etudiant::class, 'user_id');
    }

    /**
     * Obtenir les absences de l'élève
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function absences()
    {
        return $this->hasMany(Absence::class, 'etudiant_id')
            ->orderBy('date_debut', 'desc');
    }
    
    /**
     * Obtenir les absences justifiées de l'élève
     */
    public function absencesJustifiees(): HasMany
    {
        return $this->absences()->where('est_justifiee', true);
    }
    
    /**
     * Obtenir les absences non justifiées de l'élève
     */
    public function absencesNonJustifiees(): HasMany
    {
        return $this->absences()->where('est_justifiee', false);
    }
    
    /**
     * Obtenir les absences par statut
     */
    public function absencesParStatut(string $statut): HasMany
    {
        return $this->absences()->where('statut', $statut);
    }
    
    /**
     * Obtenir le nombre d'absences par statut
     */
    public function getNombreAbsencesParStatutAttribute(): array
    {
        return [
            'total' => $this->absences()->count(),
            'justifiees' => $this->absencesJustifiees()->count(),
            'non_justifiees' => $this->absencesNonJustifiees()->count(),
            'en_attente' => $this->absencesParStatut('en_attente')->count(),
            'validees' => $this->absencesParStatut('validee')->count(),
            'rejetees' => $this->absencesParStatut('rejetee')->count(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscriptions()
    {
        return $this->hasMany(\App\Models\Inscription::class, 'etudiant_id');
    }

    // Relations pour les assistants
    
    /**
     * Obtenir les absences enregistrées par l'assistant
     */
    public function absencesEnregistrees(): HasMany
    {
        return $this->hasMany(Absence::class, 'enregistre_par')
            ->orderBy('date_debut', 'desc');
    }
    
    /**
     * Obtenir les absences validées par l'utilisateur
     */
    public function absencesValidees(): HasMany
    {
        return $this->hasMany(Absence::class, 'valide_par')
            ->where('statut', 'validee')
            ->orderBy('valide_le', 'desc');
    }
    
    /**
     * Obtenir les absences rejetées par l'utilisateur
     */
    public function absencesRejetees(): HasMany
    {
        return $this->hasMany(Absence::class, 'valide_par')
            ->where('statut', 'rejetee')
            ->orderBy('valide_le', 'desc');
    }

    /**
     * Obtenir les paiements validés par l'assistant
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiementsValides()
    {
        return $this->hasMany(\App\Models\Paiement::class, 'assistant_id')
            ->where('statut', 'valide')
            ->orderBy('date_paiement', 'desc');
    }

    // Relations communes
    
    /**
     * Obtenir les matières associées à l'utilisateur
     * - Pour les professeurs: matières qu'ils enseignent
     * - Pour les élèves: matières auxquelles ils sont inscrits
     */
    public function matieres()
    {
        if ($this->isEleve()) {
            return $this->matieresEleve();
        } elseif ($this->isProfesseur()) {
            return $this->belongsToMany(Matiere::class, 'enseignements', 'professeur_id', 'matiere_id')
                ->withPivot(['niveau_id', 'filiere_id'])
                ->withTimestamps();
        }
        if ($this->isEleve()) {
            return $this->belongsToMany(Matiere::class, 'inscriptions', 'etudiant_id', 'matiere_id')
                ->withPivot(['niveau_id', 'filiere_id', 'annee_scolaire'])
                ->withTimestamps();
        }
        
        return $this->belongsToMany(Matiere::class)->withTimestamps();
    }

    /**
     * Obtenir les salaires (pour les professeurs)
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salaires()
    {
        return $this->hasMany(\App\Models\Salaire::class, 'professeur_id');
    }

    /**
     * ============================================
     * MÉTHODES D'AIDE ET UTILITAIRES
     * ============================================
     */
    
    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === RoleType::ADMIN;
    }
    
    /**
     * Vérifie si l'utilisateur est un professeur
     */
    public function isProfesseur(): bool
    {
        return $this->role === RoleType::PROFESSEUR;
    }
    
    /**
     * Vérifie si l'utilisateur est un élève
     */
    public function isEleve(): bool
    {
        return $this->role === RoleType::ELEVE;
    }
    
    /**
     * Vérifie si l'utilisateur est un assistant
     */
    public function isAssistant(): bool
    {
        return $this->role === RoleType::ASSISTANT;
    }
    
    /**
     * Vérifie si l'utilisateur a un mot de passe défini
     */
    public function hasPassword(): bool
    {
        return !empty($this->password);
    }
    
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     */
    public function hasPermission(string $permission): bool
    {
        // Vérifier si l'utilisateur a le rôle admin qui a tous les droits
        if ($this->isAdmin()) {
            return true;
        }
        
        // Vérifier les permissions via Spatie Permission
        if (method_exists($this, 'hasPermissionTo')) {
            return $this->hasPermissionTo($permission);
        }
        
        // Vérification basée sur le rôle si Spatie n'est pas utilisé
        return $this->role->hasPermission($permission);
    }

    /**
     * ============================================
     * MÉTHODES DE GESTION DES RÔLES
     * ============================================
     */

    /**
     * Définir le rôle de l'utilisateur
     */
    public function setRoleAttribute($value): void
    {
        if ($value instanceof RoleType) {
            $this->attributes['role'] = $value->value;
            return;
        }
        
        // Si c'est une chaîne, essayer de la convertir en valeur numérique
        if (is_string($value)) {
            $value = strtolower(trim($value));
            $value = match($value) {
                'admin', 'administrateur', '1' => 1,
                'professeur', 'prof', 'teacher', '2' => 2,
                'assistant', 'assist', '3' => 3,
                'eleve', 'etudiant', 'student', '4' => 4,
                default => 4, // Par défaut, on considère que c'est un élève
            };
        }
        
        // S'assurer que c'est un entier avant d'utiliser RoleType::from()
        $this->attributes['role'] = is_numeric($value) ? (int)$value : 4; // 4 = ELEVE par défaut
    }

    /**
     * ============================================
     * MÉTHODES DE NOTIFICATION
     * ============================================
     */
    
    /**
     * Définir les canaux de diffusion des notifications
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.'.$this->id;
    }

    /**
     * Envoyer une notification en temps réel à l'utilisateur
     */
    public function notifyRealTime($notification)
    {
        event(new NewNotificationEvent($notification));
    }
    
    /**
     * ============================================
     * MÉTHODES MÉTIER
     * ============================================
     */
    
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
