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
use App\Models\Presence;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Events\NewNotificationEvent;

class User extends Authenticatable implements ShouldBroadcast
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;
    
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
     * Get the channels the user receives notification broadcasts on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

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
        'niveau_id',
        'filiere_id',
        'somme_a_payer',
        'date_debut',
        'is_active',
        'parent_name',
        'parent_phone',
        'date_naissance'
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'role_label'
    ];
    
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notification_preferences' => 'array',
        'somme_a_payer' => 'decimal:2',
        'date_debut' => 'date',
        'is_active' => 'boolean',
        'date_naissance' => 'date',
    ];
    
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
        return $query->where('role', RoleType::PROFESSEUR->value);
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
    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class, 'professeur_id');
    }

    /**
     * Obtenir les matières enseignées par le professeur
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
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'etudiant_id')
            ->orderBy('date_absence', 'desc');
    }

    /**
     * Obtenir les paiements effectués par l'utilisateur (pour les élèves ou les parents)
     */
    public function paiements(): HasMany
    {
        // Vérifier si le modèle Paiement existe, sinon utiliser Paiment
        if (class_exists(Paiement::class)) {
            return $this->hasMany(Paiement::class, 'etudiant_id')
                ->orderBy('date_paiement', 'desc');
        }
        
        return $this->hasMany(Paiment::class, 'etudiant_id')
            ->orderBy('date_paiement', 'desc');
    }

    /**
     * Obtenir les notes de l'élève
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'eleve_id');
    }
    
    /**
     * Relation avec le profil enseignant (si l'utilisateur est un enseignant)
     */
    public function enseignant()
    {
        return $this->hasOne(Enseignant::class);
    }
    
    /**
     * Récupérer les présences de l'élève
     */
    public function presences()
    {
        return $this->hasMany(Presence::class, 'etudiant_id')
            ->orderBy('date_seance', 'desc')
            ->orderBy('heure_debut', 'desc');
    }

    /**
     * Obtenir les inscriptions de l'élève
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'etudiant_id');
    }

    // Relations pour les assistants
    
    /**
     * Obtenir les absences enregistrées par l'assistant
     */
    public function absencesEnregistrees(): HasMany
    {
        return $this->hasMany(Absence::class, 'assistant_id');
    }

    /**
     * Obtenir les paiements validés par l'assistant
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
        if ($this->isProfesseur()) {
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
    public function salaires(): HasMany
    {
        // Utiliser Salaires si le modèle Salaire n'existe pas
        if (class_exists(Salaire::class)) {
            return $this->hasMany(Salaire::class, 'professeur_id');
        }
        
        return $this->hasMany(Salaires::class, 'professeur_id');
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
        } else {
            $this->attributes['role'] = RoleType::from($value)->value;
        }
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
