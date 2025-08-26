<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

trait OptimizedQueries
{
    /**
     * Récupère les données avec mise en cache
     * 
     * @param string $key Clé de cache
     * @param \Closure $callback Fonction de rappel pour récupérer les données si non en cache
     * @param int $ttl Durée de vie du cache en minutes
     * @param array $tags Tags pour le cache (si supporté)
     * @return mixed
     */
    protected function cached($key, \Closure $callback, $ttl = 60, $tags = [])
    {
        if (config('cache.default') === 'array' || $ttl <= 0) {
            return $callback();
        }

        $cacheKey = $this->getCacheKey($key);
        
        if (!empty($tags) && method_exists(Cache::store(), 'tags')) {
            return Cache::tags($tags)->remember($cacheKey, now()->addMinutes($ttl), $callback);
        }
        
        return Cache::remember($cacheKey, now()->addMinutes($ttl), $callback);
    }

    /**
     * Construit une clé de cache unique
     * 
     * @param string $key
     * @return string
     */
    protected function getCacheKey($key)
    {
        $prefix = config('cache.prefix', 'eschoolar_');
        $suffix = '';
        
        // Ajouter l'identifiant de l'utilisateur connecté s'il existe
        if (auth()->check()) {
            $suffix .= '_user_' . auth()->id();
        }
        
        // Ajouter la locale actuelle
        $suffix .= '_' . app()->getLocale();
        
        return $prefix . $key . $suffix;
    }

    /**
     * Optimise une requête avec des jointures et des index
     * 
     * @param Builder $query
     * @param array $with Relations à charger
     * @param array $select Champs à sélectionner
     * @return Builder
     */
    protected function optimizeQuery(Builder $query, array $with = [], array $select = ['*'])
    {
        // Charger les relations
        if (!empty($with)) {
            $query->with($with);
        }
        
        // Sélectionner uniquement les champs nécessaires
        $query->select($select);
        
        // Forcer l'utilisation d'index si nécessaire
        if (config('database.default') === 'mysql') {
            $query->when(!empty($select) && $select !== ['*'], function ($q) use ($select) {
                $table = $q->getModel()->getTable();
                $columns = implode(',', array_map(function($col) use ($table) {
                    return "{$table}.{$col}";
                }, $select));
                
                $q->selectRaw($columns);
            });
        }
        
        return $query;
    }

    /**
     * Invalide le cache pour une clé donnée
     * 
     * @param string|array $keys Clé(s) de cache à invalider
     * @param array $tags Tags de cache (si supporté)
     * @return void
     */
    protected function invalidateCache($keys, $tags = [])
    {
        $keys = is_array($keys) ? $keys : [$keys];
        
        foreach ($keys as $key) {
            $cacheKey = $this->getCacheKey($key);
            
            if (!empty($tags) && method_exists(Cache::store(), 'tags')) {
                Cache::tags($tags)->forget($cacheKey);
            } else {
                Cache::forget($cacheKey);
            }
        }
    }

    /**
     * Récupère les statistiques avec mise en cache
     * 
     * @param array $params Paramètres de la requête
     * @param \Closure $callback Fonction pour calculer les statistiques
     * @param int $ttl Durée de vie du cache en minutes
     * @return mixed
     */
    protected function getCachedStatistics($params, \Closure $callback, $ttl = 60)
    {
        $key = 'stats_' . md5(serialize($params));
        
        return $this->cached($key, $callback, $ttl, ['statistiques']);
    }
}
