<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\User;
use App\Models\Etudiant;
use Carbon\Carbon;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Durée de vie du cache en minutes
     */
    protected $cacheTtl = 60; // 1 heure par défaut

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerCacheMacros();
        $this->registerModelObservers();
    }

    /**
     * Enregistre les macros de cache personnalisées
     */
    protected function registerCacheMacros(): void
    {
        // Macro pour le cache des matières avec filtre
        Cache::macro('matieres', function (\Closure $callback, $filters = [], $ttl = null) {
            $key = 'matieres_' . md5(serialize($filters));
            $ttl = $ttl ?? $this->cacheTtl;
            
            return Cache::remember($key, now()->addMinutes($ttl), function () use ($callback, $filters) {
                return $callback($filters);
            });
        });

        // Macro pour le cache des classes avec filtre
        Cache::macro('classes', function (\Closure $callback, $filters = [], $ttl = null) {
            $key = 'classes_' . md5(serialize($filters));
            $ttl = $ttl ?? $this->cacheTtl;
            
            return Cache::remember($key, now()->addMinutes($ttl), function () use ($callback, $filters) {
                return $callback($filters);
            });
        });

        // Macro pour le cache des étudiants avec filtre
        Cache::macro('etudiants', function (\Closure $callback, $filters = [], $ttl = null) {
            $key = 'etudiants_' . md5(serialize($filters));
            $ttl = $ttl ?? $this->cacheTtl;
            
            return Cache::remember($key, now()->addMinutes($ttl), function () use ($callback, $filters) {
                return $callback($filters);
            });
        });

        // Macro pour les statistiques de présence
        Cache::macro('statistiquesPresences', function ($params, \Closure $callback, $ttl = null) {
            $key = 'stats_presences_' . md5(serialize($params));
            $ttl = $ttl ?? $this->cacheTtl;
            
            return Cache::remember($key, now()->addMinutes($ttl), $callback);
        });
    }

    /**
     * Enregistre les observateurs pour invalider le cache lors des mises à jour
     */
    protected function registerModelObservers(): void
    {
        // Observer pour le modèle Matiere
        Matiere::created(function ($matiere) {
            Cache::tags(['matieres'])->flush();
        });
        
        Matiere::updated(function ($matiere) {
            Cache::tags(['matieres'])->flush();
        });
        
        Matiere::deleted(function ($matiere) {
            Cache::tags(['matieres'])->flush();
        });

        // Observer pour le modèle Classe
        Classe::created(function ($classe) {
            Cache::tags(['classes'])->flush();
        });
        
        Classe::updated(function ($classe) {
            Cache::tags(['classes'])->flush();
        });
        
        Classe::deleted(function ($classe) {
            Cache::tags(['classes'])->flush();
        });

        // Observer pour le modèle Etudiant
        Etudiant::created(function ($etudiant) {
            Cache::tags(['etudiants'])->flush();
        });
        
        Etudiant::updated(function ($etudiant) {
            Cache::tags(['etudiants'])->flush();
        });
        
        Etudiant::deleted(function ($etudiant) {
            Cache::tags(['etudiants'])->flush();
        });
    }
}
