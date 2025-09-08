<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Matiere extends Model
{
    /**
     * Les matières fixes du système avec leurs niveaux associés
     */
    const MATHS = 'Mathématiques';
    const SVT = 'SVT';
    const PHYSIQUE = 'Physique';
    const COMMUNICATION_FR = 'Communication Française';
    const COMMUNICATION_ANG = 'Communication Anglaise';

    /**
     * Niveaux scolaires disponibles avec leurs libellés complets
     */
    const NIVEAU_PRIMAIRE_3 = 'primaire_3';
    const NIVEAU_PRIMAIRE_4 = 'primaire_4';
    const NIVEAU_PRIMAIRE_5 = 'primaire_5';
    const NIVEAU_PRIMAIRE_6 = 'primaire_6';
    const NIVEAU_COLLEGE_1 = 'college_1';
    const NIVEAU_COLLEGE_2 = 'college_2';
    const NIVEAU_COLLEGE_3 = 'college_3';
    const NIVEAU_TRONC_COMMUN = 'tronc_commun';
    const NIVEAU_1BAC_SM = '1bac_sm';
    const NIVEAU_1BAC_SV = '1bac_sv';
    const NIVEAU_2BAC_SM = '2bac_sm';
    const NIVEAU_2BAC_SV = '2bac_sv';

    /**
     * Prix par défaut par niveau (en DH)
     */
    const PRIX_PAR_NIVEAU = [
        self::NIVEAU_PRIMAIRE_3 => 180,
        self::NIVEAU_PRIMAIRE_4 => 190,
        self::NIVEAU_PRIMAIRE_5 => 200,
        self::NIVEAU_PRIMAIRE_6 => 210,
        self::NIVEAU_COLLEGE_1 => 230,
        self::NIVEAU_COLLEGE_2 => 240,
        self::NIVEAU_COLLEGE_3 => 250,
        self::NIVEAU_TRONC_COMMUN => 270,
        self::NIVEAU_1BAC_SM => 280,
        self::NIVEAU_1BAC_SV => 280,
        self::NIVEAU_2BAC_SM => 300,
        self::NIVEAU_2BAC_SV => 300,
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'description',
        'niveau',
        'prix_mensuel',    // Prix mensuel par élève pour cette matière
        'prix_trimestriel', // Prix trimestriel (avec réduction)
        'couleur',         // Pour l'affichage dans le calendrier/interface
        'icone',           // Icône FontAwesome pour la matière
        'est_active',      // Si la matière est activée
        'est_fixe',        // Si c'est une matière fixe (obligatoire pour le niveau)
    ];
    
    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'prix_mensuel' => 'decimal:2',
        'prix_trimestriel' => 'decimal:2',
        'est_active' => 'boolean',
        'est_fixe' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Les attributs par défaut pour les nouvelles instances.
     *
     * @var array
     */
    protected $attributes = [
        'est_active' => true,
        'est_fixe' => false,
        'prix_trimestriel' => null,
    ];
    
    /**
     * Les attributs qui doivent être ajoutés au modèle lors de la sélection.
     *
     * @var array
     */
    protected $appends = [
        'libelle_complet',
        'prix_mensuel_formate',
        'niveau_libelle',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        // Calculer automatiquement le prix trimestriel s'il n'est pas défini
        static::saving(function ($model) {
            if (empty($model->prix_trimestriel) && !empty($model->prix_mensuel)) {
                // 10% de réduction pour un paiement trimestriel
                $model->prix_trimestriel = $model->prix_mensuel * 3 * 0.9;
            }
        });
    }

    /**
     * Les niveaux scolaires disponibles avec leurs libellés
     *
     * @return array
     */
    public static function getNiveauxDisponibles(): array
    {
        return [
            'primaire_3' => '3ème année primaire',
            'primaire_4' => '4ème année primaire',
            'primaire_5' => '5ème année primaire',
            'primaire_6' => '6ème année primaire',
            'college_1' => '1ère année collège',
            'college_2' => '2ème année collège',
            'college_3' => '3ème année collège',
            'tronc_commun' => 'Tronc commun',
            '1bac_sm' => '1ère année bac - Sciences Maths',
            '1bac_sv' => '1ère année bac - Sciences Physiques',
            '2bac_sm' => '2ème année bac - Sciences Maths',
            '2bac_sv' => '2ème année bac - Sciences Physiques',
        ];
    }
    
    /**
     * Obtenir le libellé complet du niveau
     *
     * @return string
     */
    public function getNiveauLibelleAttribute(): string
    {
        return self::getNiveauxDisponibles()[$this->niveau] ?? $this->niveau;
    }
    
    /**
     * Obtenir le groupe de niveau (primaire, collège, lycée)
     *
     * @return string
     */
    public function getGroupeNiveauAttribute(): string
    {
        if (str_starts_with($this->niveau, 'primaire_')) {
            return 'primaire';
        } elseif (str_starts_with($this->niveau, 'college_')) {
            return 'college';
        } elseif (in_array($this->niveau, ['tronc_commun', '1bac_sm', '1bac_sv', '2bac_sm', '2bac_sv'])) {
            return 'lycee';
        }
        
        return 'autre';
    }
    
    /**
     * Obtenir le code court du niveau (ex: P3, C1, TC, 1SM, etc.)
     *
     * @return string
     */
    public function getCodeNiveauAttribute(): string
    {
        if (str_starts_with($this->niveau, 'primaire_')) {
            return 'P' . substr($this->niveau, -1);
        } elseif (str_starts_with($this->niveau, 'college_')) {
            return 'C' . substr($this->niveau, -1);
        } elseif ($this->niveau === 'tronc_commun') {
            return 'TC';
        } elseif (str_starts_with($this->niveau, '1bac_')) {
            return '1' . strtoupper(substr($this->niveau, -2, 1));
        } elseif (str_starts_with($this->niveau, '2bac_')) {
            return '2' . strtoupper(substr($this->niveau, -2, 1));
        }
        
        return substr(strtoupper($this->niveau), 0, 2);
    }

    /**
     * Relation avec les élèves inscrits à cette matière
     */
    public function eleves(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'eleve_matiere', 'matiere_id', 'eleve_id')
            ->where('role', User::ROLE_ELEVE)
            ->withTimestamps();
    }

    /**
     * Relation avec les professeurs qui enseignent cette matière
     */
    public function professeurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'professeur_matiere', 'matiere_id', 'professeur_id')
            ->where('role', User::ROLE_PROFESSEUR)
            ->withPivot('est_responsable')
            ->withTimestamps();
    }

    /**
     * Relation avec les absences pour cette matière
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Obtenir le professeur responsable de cette matière
     */
    public function getProfesseurResponsableAttribute()
    {
        return $this->professeurs()
            ->wherePivot('est_responsable', true)
            ->first();
    }

    /**
     * Obtenir le nombre d'élèves inscrits à cette matière
     */
    public function getNombreElevesAttribute(): int
    {
        return $this->eleves()->count();
    }

    /**
     * Obtenir le chiffre d'affaires mensuel pour cette matière
     */
    public function getChiffreAffairesMensuelAttribute(): float
    {
        return $this->nombre_eleves * $this->prix_mensuel;
    }

    /**
     * Obtenir la liste des matières fixes avec leurs détails
     */
    public static function getMatieresFixes(): array
    {
        return [
            [
                'nom' => self::MATHS,
                'description' => 'Mathématiques pour tous les niveaux',
                'couleur' => '#3498db',
                'icone' => 'fas fa-square-root-alt',
            ],
            [
                'nom' => self::SVT,
                'description' => 'Sciences de la Vie et de la Terre',
                'couleur' => '#2ecc71',
                'icone' => 'fas fa-leaf',
            ],
            [
                'nom' => self::PHYSIQUE,
                'description' => 'Physique-Chimie',
                'couleur' => '#e74c3c',
                'icone' => 'fas fa-atom',
            ],
            [
                'nom' => self::COMMUNICATION_FR,
                'description' => 'Communication en langue française',
                'couleur' => '#9b59b6',
                'icone' => 'fas fa-language',
            ],
            [
                'nom' => self::COMMUNICATION_ANG,
                'description' => 'Communication en langue anglaise',
                'couleur' => '#f39c12',
                'icone' => 'fas fa-globe-africa',
            ],
        ];
    }

    /**
     * Initialiser les matières fixes dans la base de données
     */
    public static function initialiserMatieresFixes(): void
    {
        $matieres = self::getMatieresFixes();
        
        DB::beginTransaction();
        
        try {
            foreach (self::getNiveauxDisponibles() as $niveauKey => $niveauLabel) {
                foreach ($matieres as $matiere) {
                    // Vérifier si la matière existe déjà pour ce niveau
                    $exists = self::where('nom', $matiere['nom'])
                        ->where('niveau', $niveauKey)
                        ->exists();
                    
                    if (!$exists) {
                        self::create([
                            'nom' => $matiere['nom'],
                            'description' => $matiere['description'],
                            'niveau' => $niveauKey,
                            'prix_mensuel' => self::PRIX_PAR_NIVEAU[$niveauKey],
                            'couleur' => $matiere['couleur'],
                            'est_active' => true,
                        ]);
                    }
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtenir le nom complet avec le niveau
     */
    public function getLibelleCompletAttribute()
    {
        return "{$this->nom} ({$this->getNiveauLibelle()})";
    }
    
    /**
     * Récupère le prix mensuel de la matière
     * Si un niveau est spécifié, retourne le prix pour ce niveau
     * Sinon, utilise le niveau par défaut ou le prix fixe de la matière
     * 
     * @param string|null $niveau Le niveau scolaire (optionnel)
     * @return float Le prix mensuel
     */
    public function getPrixMensuel($niveau = null)
    {
        // Si un prix fixe est défini pour la matière, on l'utilise
        if ($this->prix_mensuel) {
            return (float) $this->prix_mensuel;
        }
        
        // Sinon, on utilise le prix par niveau
        $niveau = $niveau ?? $this->niveau;
        return self::PRIX_PAR_NIVEAU[$niveau] ?? 0;
    }
    
    /**
     * Accesseur pour le prix mensuel formaté
     */
    public function getPrixMensuelFormateAttribute()
    {
        return number_format($this->getPrixMensuel(), 2, ',', ' ') . ' MAD';
    }

    /**
     * Scope pour les matières actives
     */
    public function scopeActive($query)
    {
        return $query->where('est_active', true);
    }
    
    /**
     * Scope pour les matières par niveau
     */
    public function scopeParNiveau($query, $niveau)
    {
        return $query->where('niveau', $niveau);
    }

    /**
     * Vérifie si la matière est une matière fixe
     */
    public function isMatiereFixe(): bool
    {
        $nomsMatieresFixes = array_column(self::getMatieresFixes(), 'nom');
        return in_array($this->nom, $nomsMatieresFixes);
    }
    
    /**
     * Obtenir le libellé du niveau
     */
    public function getLibelleNiveauAttribute(): string
    {
        return self::getNiveauxDisponibles()[$this->niveau] ?? 'Inconnu';
    }
    
    /**
     * Obtenir le nom complet avec le niveau
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->nom} - {$this->libelle_niveau}";
    }
}
