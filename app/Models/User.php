<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Enums\RoleType;
use App\Models\Paiement;
use App\Models\PaiementProfesseur;
use App\Models\Salaire;
use App\Models\Absence;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Events\NewNotificationEvent;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     *
     * @param string|int|array $role Rôle ou tableau de rôles à vérifier
     * @return bool
     */
    public function hasRole($role): bool
    {
        // Vérification directe du rôle administrateur pour éviter la récursion
        $userRole = $this->attributes['role'] ?? null;
        if ($userRole === RoleType::ADMIN->value || $userRole === 1 || $userRole === '1') {
            return true;
        }
        
        // Si on cherche à vérifier plusieurs rôles
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
            return false;
        }
        
        // Si le rôle est une chaîne comme 'admin', le convertir en valeur d'énumération
        if (is_string($role) && defined("App\\Enums\\RoleType::$role")) {
            $role = constant("App\\Enums\\RoleType::$role")->value;
        }
        
        // Si le rôle est un objet RoleType, utiliser sa valeur
        if ($role instanceof \App\Enums\RoleType) {
            $role = $role->value;
        }
        
        // Récupérer le rôle de l'utilisateur
        $userRole = $this->role;
        
        // Si l'utilisateur n'a pas de rôle, retourner false
        if (is_null($userRole)) {
            return false;
        }
        
        // Comparaison numérique ou chaîne selon le type du rôle
        if (is_numeric($role)) {
            return (int)$userRole === (int)$role;
        }
        
        return $userRole === $role;
    }
    
    /**
     * Vérifie si l'utilisateur est un administrateur
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        $userRole = $this->attributes['role'] ?? null;
        return $userRole === RoleType::ADMIN->value || $userRole === 1 || $userRole === '1';
    }
    
    /**
     * Vérifie si l'utilisateur est un professeur
     * 
     * @return bool
     */
    public function isProfesseur(): bool
    {
        $userRole = $this->attributes['role'] ?? null;
        return $userRole === RoleType::PROFESSEUR->value || $userRole === 2 || $userRole === '2';
    }
    
    /**
     * Vérifie si l'utilisateur est un élève
     * 
     * @return bool
     */
    public function isEleve(): bool
    {
        $userRole = $this->attributes['role'] ?? null;
        return $userRole === RoleType::ELEVE->value || $userRole === 4 || $userRole === '4';
    }
    
    /**
     * Vérifie si l'utilisateur est un assistant
     * 
     * @return bool
     */
    public function isAssistant(): bool
    {
        $userRole = $this->attributes['role'] ?? null;
        return $userRole === RoleType::ASSISTANT->value || $userRole === 3 || $userRole === '3';
    }
    
    // Constantes de rôle
    const ROLE_ADMIN = 'admin';
    const ROLE_PROFESSEUR = 'professeur';
    const ROLE_ELEVE = 'eleve';
    const ROLE_ASSISTANT = 'assistant';
    
    /**
     * Vérifie si l'utilisateur est un administrateur (accesseur)
     * 
     * @return bool
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

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
        'prenom',
        'notify_by_email',
        'email',
        'password',
        'role',
        'status',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'telephone',
        'niveau_id',
        'filiere_id',
        'nom_pere',
        'telephone_pere',
        'pourcentage_remuneration', 
        'date_embauche',       
        'nom_mere',                
        'telephone_mere',
        'email_verified_at',
        'is_active',
        'remember_token'
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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];
    
    /**
     * Les attributs par défaut
     *
     * @var array
     */
    protected $attributes = [
        'role' => self::ROLE_ELEVE,
        'status' => self::STATUS_ACTIF,
        'is_active' => true
    ];
    
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
     * Get the user's role.
     *
     * @return string
     */
    public function role()
    {
        return $this->attributes['role'] ?? null;
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
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\Models\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)
            ->where('status', 'actif')
            ->first();
    }

    /**
     * Get the payments made by this user (for admin users who record payments)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiementsEnregistres(): HasMany
    {
        return $this->hasMany(PaiementProfesseur::class, 'enregistre_par');
    }
    
    /**
     * Get the payments for this user
     * - For teachers: payments received
     * - For students: payments made
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiementsParRole()
    {
        if ($this->hasRole('professeur')) {
            return $this->hasMany(PaiementProfesseur::class, 'professeur_id')
                ->orderBy('mois', 'desc');
        }
        
        if ($this->hasRole('eleve')) {
            return $this->hasMany(Paiement::class, 'etudiant_id')
                ->orderBy('date_paiement', 'desc');
        }
        
        // Par défaut, retourner une relation vide
        return $this->hasMany(Paiement::class, 'id', 'id')->whereRaw('1=0');
    }
    
    /**
     * Get the active payments for this teacher
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiementsActifs(): HasMany
    {
        return $this->paiementsParRole()->where('statut', 'paye');
    }
    
    /**
     * Get the pending payments for this teacher
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiementsEnAttente(): HasMany
    {
        return $this->paiementsParRole()->whereIn('statut', ['en_retard', 'impaye']);
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
        return $this->paiementsParRole()
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
        $query = $this->paiementsParRole()->where('statut', 'paye');
        
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
     * @param mixed $value La valeur brute du rôle (peut être un entier, une chaîne, null, etc.)
     * @return \App\Enums\RoleType
     * 
     * @throws \UnexpectedValueException Si la valeur du rôle est invalide
     */
    public function getRoleAttribute($value): RoleType
    {
        // Si c'est déjà une instance de RoleType, la retourner directement
        if ($value instanceof RoleType) {
            return $value;
        }
        
        // Si c'est un entier, essayer de le convertir en RoleType
        if (is_numeric($value)) {
            try {
                return RoleType::from((int)$value);
            } catch (\ValueError $e) {
                // En cas d'erreur, on retourne le rôle par défaut
                return RoleType::ELEVE;
            }
        }
        
        // Si c'est une chaîne, essayer de la convertir en RoleType
        if (is_string($value)) {
            $value = strtolower(trim($value));
            
            // Vérifier si c'est un nom de constante valide
            if (defined("App\\Enums\\RoleType::$value")) {
                return constant("App\\Enums\\RoleType::$value");
            }
            
            // Essayer de faire correspondre avec des valeurs connues
            return match($value) {
                '1', 'admin', 'administrateur' => RoleType::ADMIN,
                '2', 'professeur', 'prof', 'teacher' => RoleType::PROFESSEUR,
                '3', 'assistant', 'assist' => RoleType::ASSISTANT,
                '4', 'eleve', 'etudiant', 'student' => RoleType::ELEVE,
                default => RoleType::ELEVE, // Par défaut, on considère que c'est un élève
            };
        }
        
        // Si la valeur est null ou d'un type inattendu, retourner le rôle par défaut
        return RoleType::ELEVE;
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
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', RoleType::ADMIN->value);
    }

    /**
     * Scope pour les professeurs
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeProfesseurs($query)
    {
        return $query->where('role', RoleType::PROFESSEUR->value);
    }

    /**
     * Scope pour les assistants
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAssistants($query)
    {
        return $query->where('role', RoleType::ASSISTANT->value);
    }

    /**
     * Scope pour les élèves
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeEleves($query)
    {
        return $query->where('role', RoleType::ELEVE->value);
    }

    /**
     * Scope pour les utilisateurs actifs
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
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
     * Relation avec les enseignements (pour les professeurs)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class, 'professeur_id');
    }

    /**
     * Obtenir les matières enseignées par le professeur
     */
    /**
     * Relation avec les matières enseignées (pour les professeurs)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function matieresEnseignees(): BelongsToMany
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
     * Relation avec les absences (pour les élèves)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'eleve_id')
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
     * Obtient les paiements associés à l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'eleve_id');
    }

    /**
     * Obtient les paiements validés par l'assistant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiementsValides(): HasMany
    {
        return $this->hasMany(Paiement::class, 'assistant_id')
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
            return $this->belongsToMany(Matiere::class, 'eleve_matiere', 'eleve_id', 'matiere_id')
                ->withTimestamps();
        } elseif ($this->isProfesseur()) {
            return $this->belongsToMany(Matiere::class, 'enseignements', 'professeur_id', 'matiere_id')
                ->withPivot(['niveau_id', 'filiere_id'])
                ->withTimestamps();
        }
        
        return $this->belongsToMany(Matiere::class)->withTimestamps();
    }

    /**
     * Relation avec les salaires (pour les professeurs)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salaires(): HasMany
    {
        return $this->hasMany(Salaire::class, 'professeur_id');
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
        
        // Si le rôle est un entier, le convertir en objet RoleType
        $role = is_numeric($this->role) ? RoleType::from($this->role) : $this->role;
        
        // Vérification basée sur le rôle
        return method_exists($role, 'hasPermission') ? $role->hasPermission($permission) : false;
    }


    /**
     * Définir le rôle de l'utilisateur
     * 
     * @param mixed $value Peut être un objet RoleType, une chaîne ou un entier
     * @return void
     */
    public function setRoleAttribute($value): void
    {
        // Si c'est déjà une instance de RoleType, utiliser sa valeur
        if ($value instanceof RoleType) {
            $this->attributes['role'] = $value->value;
            return;
        }
        
        // Si c'est une chaîne, essayer de la convertir en valeur numérique
        if (is_string($value)) {
            $value = strtolower(trim($value));
            
            // Vérifier si c'est un nom de constante RoleType valide
            if (defined("App\\Enums\\RoleType::$value")) {
                $role = constant("App\\Enums\\RoleType::$value");
                $this->attributes['role'] = $role->value;
                return;
            }
            
            // Sinon, essayer de faire correspondre avec les valeurs connues
            $value = match($value) {
                'admin', 'administrateur', '1' => RoleType::ADMIN->value,
                'professeur', 'prof', 'teacher', '2' => RoleType::PROFESSEUR->value,
                'assistant', 'assist', '3' => RoleType::ASSISTANT->value,
                'eleve', 'etudiant', 'student', '4' => RoleType::ELEVE->value,
                default => RoleType::ELEVE->value, // Par défaut, on considère que c'est un élève
            };
        }
        
        // S'assurer que c'est un entier valide
        $intValue = is_numeric($value) ? (int)$value : RoleType::ELEVE->value;
        
        // Vérifier que la valeur est valide
        if (!in_array($intValue, [
            RoleType::ADMIN->value,
            RoleType::PROFESSEUR->value,
            RoleType::ASSISTANT->value,
            RoleType::ELEVE->value
        ])) {
            $intValue = RoleType::ELEVE->value; // Valeur par défaut si invalide
        }
        
        $this->attributes['role'] = $intValue;
    }
}
