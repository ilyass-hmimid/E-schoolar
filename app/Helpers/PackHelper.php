<?php

namespace App\Helpers;

use App\Models\Pack;
use Illuminate\Support\Facades\Cache;

class PackHelper
{
    /**
     * Récupère les statistiques des packs
     *
     * @return array
     */
    public static function getStats(): array
    {
        return Cache::remember('packs.stats', now()->addHours(1), function () {
            return [
                'total' => Pack::count(),
                'active' => Pack::where('est_actif', true)->count(),
                'average_price' => (float) Pack::avg('prix') ?? 0,
                'total_sales' => self::calculateTotalSales(),
            ];
        });
    }

    /**
     * Calcule le total des ventes de tous les packs
     *
     * @return float
     */
    public static function calculateTotalSales(): float
    {
        return (float) \DB::table('ventes')
            ->join('pack_vente', 'ventes.id', '=', 'pack_vente.vente_id')
            ->sum('pack_vente.prix');
    }

    /**
     * Récupère les packs les plus populaires
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopularPacks(int $limit = 3)
    {
        return Cache::remember("packs.popular.{$limit}", now()->addHours(12), function () use ($limit) {
            return Pack::withCount('ventes')
                ->where('est_actif', true)
                ->orderBy('est_populaire', 'desc')
                ->orderBy('ventes_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Formate le prix avec la devise
     *
     * @param float $price
     * @return string
     */
    public static function formatPrice(float $price): string
    {
        return number_format($price, 2, ',', ' ') . ' DH';
    }

    /**
     * Calcule le pourcentage d'économie pour un prix promotionnel
     *
     * @param float $originalPrice
     * @param float $salePrice
     * @return int
     */
    public static function calculateDiscountPercentage(float $originalPrice, float $salePrice): int
    {
        if ($originalPrice <= 0 || $salePrice >= $originalPrice) {
            return 0;
        }

        return (int) round((($originalPrice - $salePrice) / $originalPrice) * 100);
    }

    /**
     * Génère un slug unique pour un pack
     *
     * @param string $name
     * @return string
     */
    public static function generateSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $count = Pack::where('slug', 'LIKE', "{$slug}%")->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Récupère les types de packs disponibles
     *
     * @return array
     */
    public static function getPackTypes(): array
    {
        return collect(config('packs.types'))
            ->mapWithKeys(function ($type, $key) {
                return [
                    $key => [
                        'name' => $type['name'],
                        'icon' => $type['icon'],
                        'color' => $type['color'],
                    ]
                ];
            })
            ->toArray();
    }

    /**
     * Vérifie si un pack peut être supprimé
     *
     * @param Pack $pack
     * @return bool
     */
    public static function canBeDeleted(Pack $pack): bool
    {
        return $pack->ventes()->count() === 0;
    }

    /**
     * Récupère les packs similaires
     *
     * @param Pack $pack
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSimilarPacks(Pack $pack, int $limit = 3)
    {
        return Pack::where('type', $pack->type)
            ->where('id', '!=', $pack->id)
            ->where('est_actif', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère le prix affiché (prix promotionnel si disponible, sinon prix normal)
     *
     * @param Pack $pack
     * @return float
     */
    public static function getDisplayPrice(Pack $pack): float
    {
        return $pack->prix_promo ?? $pack->prix;
    }
}
