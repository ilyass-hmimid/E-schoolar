<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigurationSalaire extends Model
{
    use SoftDeletes;

    protected $table = 'configuration_salaires';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'matiere_id',
        'prix_unitaire',
        'commission_prof',
        'prix_min',
        'prix_max',
        'est_actif',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'commission_prof' => 'decimal:2',
        'prix_min' => 'decimal:2',
        'prix_max' => 'decimal:2',
        'est_actif' => 'boolean',
    ];

    /**
     * Relations
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Scopes
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeParMatiere($query, $matiereId)
    {
        return $query->where('matiere_id', $matiereId);
    }

    /**
     * Récupère la configuration pour une matière donnée
     *
     * @param int $matiereId
     * @return ConfigurationSalaire
     */
    public static function getForMatiere($matiereId)
    {
        return static::where('matiere_id', $matiereId)
            ->actifs()
            ->firstOr(function () use ($matiereId) {
                // Retourne une configuration par défaut si aucune n'est trouvée
                return new static([
                    'matiere_id' => $matiereId,
                    'prix_unitaire' => 100, // Valeur par défaut
                    'commission_prof' => 30, // 30% par défaut
                    'est_actif' => true,
                ]);
            });
    }
}
