<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Vente;

class Pack extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'slug',
        'description',
        'type',
        'prix',
        'prix_promo',
        'duree_jours',
        'est_actif',
        'est_populaire',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'prix' => 'decimal:2',
        'prix_promo' => 'decimal:2',
        'est_actif' => 'boolean',
        'est_populaire' => 'boolean',
        'duree_jours' => 'integer',
    ];

    /**
     * Récupère les types de packs disponibles depuis la configuration.
     *
     * @return array
     */
    public static function getTypes()
    {
        return array_map(function($type) {
            return $type['name'];
        }, config('packs.types', []));
    }

    /**
     * Les types de packs disponibles.
     *
     * @deprecated Utiliser getTypes() à la place
     * @var array
     */
    public static $types = [
        'cours' => 'Cours',
        'abonnement' => 'Abonnement',
        'formation' => 'Formation',
        'autre' => 'Autre',
    ];

    /**
     * Les dates qui doivent être traitées comme des dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation many-to-many avec le modèle Matiere.
     */
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'matiere_pack')
            ->withPivot('nombre_heures_par_matiere')
            ->withTimestamps();
    }

    /**
     * Relation many-to-many avec le modèle Vente.
     */
    public function ventes()
    {
        return $this->belongsToMany(Vente::class, 'pack_vente')
            ->withPivot('prix_unitaire', 'quantite', 'prix_total')
            ->withTimestamps();
    }

    /**
     * Relation avec les inscriptions (étudiants qui ont souscrit à ce pack).
     */
    /**
     * Relation avec les tarifs du pack.
     */
    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }

    /**
     * Relation avec les tarifs actifs du pack.
     */
    public function tarifsActifs()
    {
        return $this->tarifs()->actif();
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'pack_id');
    }

    /**
     * Générateur d'URL pour le pack.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Boot du modèle.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($pack) {
            if (empty($pack->slug)) {
                $pack->slug = Str::slug($pack->nom);
            }
        });

        static::updating(function ($pack) {
            if ($pack->isDirty('nom') && empty($pack->slug)) {
                $pack->slug = Str::slug($pack->nom);
            }
        });
    }

    /**
     * Règles de validation pour les packs
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packs,slug',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', array_keys(config('packs.types', []))),
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0|lt:prix',
            'duree_jours' => 'required|integer|min:1',
            'est_actif' => 'boolean',
            'est_populaire' => 'boolean',
        ];
    }

    /**
     * Scope pour les packs actifs.
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }
    
    /**
     * Scope pour les packs populaires.
     */
    public function scopePopular($query)
    {
        return $query->where('est_populaire', true);
    }
    
    /**
     * Vérifie si le pack peut être supprimé.
     * Un pack ne peut pas être supprimé s'il a des ventes associées.
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->ventes()->count() === 0;
    }
    
    /**
     * Retourne le prix affiché (prix promo si disponible, sinon prix normal).
     *
     * @return string
     */
    public function getDisplayPrice(): string
    {
        return $this->prix_promo ?? $this->prix;
    }
    
    /**
     * Calcule le pourcentage de réduction.
     *
     * @return float|null Retourne null si pas de promotion
     */
    public function getDiscountPercentage(): ?float
    {
        if (!$this->prix_promo || $this->prix <= 0) {
            return null;
        }
        
        return round((($this->prix - $this->prix_promo) / $this->prix) * 100, 2);
    }

    /**
     * Scope pour les packs actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Formate le prix avec le symbole de la devise.
     */
    public function getPrixFormateAttribute(): string
    {
        return number_format($this->prix, 2, ',', ' ') . ' MAD';
    }

    /**
     * Calcule le nombre total d'heures pour une matière spécifique dans ce pack.
     */
    public function getHeuresPourMatiere(int $matiereId): int
    {
        $matiere = $this->matieres()->where('matiere_id', $matiereId)->first();
        return $matiere ? (int) $matiere->pivot->nombre_heures_par_matiere : 0;
    }

    /**
     * Vérifie si le pack contient une matière spécifique.
     */
    public function contientMatiere(int $matiereId): bool
    {
        return $this->matieres()->where('matiere_id', $matiereId)->exists();
    }

    /**
     * Ajoute une matière au pack avec un nombre d'heures spécifié.
     */
    public function ajouterMatiere(int $matiereId, int $nombreHeures): void
    {
        $this->matieres()->syncWithoutDetaching([
            $matiereId => ['nombre_heures_par_matiere' => $nombreHeures]
        ]);
    }

    /**
     * Supprime une matière du pack.
     */
    public function supprimerMatiere(int $matiereId): void
    {
        $this->matieres()->detach($matiereId);
    }

    /**
     * Met à jour le nombre d'heures pour une matière spécifique dans le pack.
     */
    public function mettreAJourHeuresMatiere(int $matiereId, int $nombreHeures): void
    {
        $this->matieres()->updateExistingPivot($matiereId, [
            'nombre_heures_par_matiere' => $nombreHeures
        ]);
    }
}
