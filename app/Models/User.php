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
     * Vérifie si l'utilisateur a le rôle spécifié
     *
     * @param string|array $role Rôle ou tableau de rôles à vérifier
     * @return bool
     */
    public function hasRole($role): bool
    {
        // Si l'utilisateur est admin, il a automatiquement tous les rôles
        if ($this->is_admin) {
            return true;
        }
        
        // Convertir le rôle en entier si nécessaire
        $userRole = is_numeric($this->role) ? (int)$this->role : $this->role;
        
        if (is_array($role)) {
            $role = array_map(function($r) {
                if (is_string($r) && defined("App\\Enums\\RoleType::$r")) {
                    return constant("App\\Enums\\RoleType::$r")->value;
                }
                return $r;
            }, $role);
            
            return in_array($userRole, $role);
        }
        
        // Si le rôle est une chaîne comme 'admin', le convertir en valeur d'énumération
        if (is_string($role) && defined("App\\Enums\\RoleType::$role")) {
            $role = constant("App\\Enums\\RoleType::$role")->value;
        }
        
        // Vérifier si le rôle demandé correspond au rôle de l'utilisateur
        return $userRole === $role;
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
        
        // Si le rôle est un entier, le convertir en objet RoleType
        $role = is_numeric($this->role) ? RoleType::from($this->role) : $this->role;
        
        // Vérification basée sur le rôle
        return method_exists($role, 'hasPermission') ? $role->hasPermission($permission) : false;
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
     * ============================================
     * MÉTHODES MÉTIER
     * ============================================
     */
    


}
