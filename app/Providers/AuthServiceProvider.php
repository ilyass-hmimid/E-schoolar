<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Paiement;
use App\Models\Absence;
use App\Models\Note;
use App\Policies\UserPolicy;
use App\Policies\PaiementPolicy;
use App\Policies\AbsencePolicy;
use App\Policies\NotePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    /**
     * Les mappages de modèles vers leurs politiques.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Modèle User
        User::class => UserPolicy::class,
        
        // Modèle Paiement (Élèves)
        Paiement::class => PaiementPolicy::class,
        
        // Modèle PaiementProfesseur
        \App\Models\PaiementProfesseur::class => PaiementPolicy::class,
        
        // Modèle Absence
        Absence::class => AbsencePolicy::class,
        
        // Modèle Note
        Note::class => NotePolicy::class,
        
        // Modèle Classe
        \App\Models\Classe::class => \App\Policies\ClassePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    /**
     * Enregistre les services d'authentification/autorisation.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Définition des capacités basées sur les rôles
        // Ces capacités peuvent être utilisées avec @can dans les vues
        Gate::before(function (User $user, string $ability) {
            // L'admin a tous les accès
            if ($user->hasRole('admin')) {
                return true;
            }
        });
        
        // Définition des portes (gates) personnalisées
        $this->defineGates();
    }
    
    /**
     * Définit les portes (gates) personnalisées pour l'autorisation.
     */
    protected function defineGates(): void
    {
        // Exemple de porte personnalisée pour la gestion des paramètres
        Gate::define('manage-settings', function (User $user) {
            return $user->hasRole('admin');
        });
        
        // Porte pour la gestion des utilisateurs
        Gate::define('manage-users', function (User $user) {
            return $user->hasRole('admin');
        });
        
        // Porte pour la gestion des paiements
        Gate::define('manage-payments', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant']);
        });
        
        // Porte pour la gestion des présences
        Gate::define('manage-attendance', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant', 'professeur']);
        });
    }

    /**
     * Define custom gates for specific permissions
     */
    protected function defineCustomGates(): void
    {
        // Allow professors to manage their classes
        Gate::define('manage-class', function (User $user, $class) {
            if ($user->hasRole('admin')) {
                return true;
            }
            
            if ($user->hasRole('professor')) {
                return $user->classes()->where('id', $class->id)->exists();
            }
            
            return false;
        });

        // Allow professors to manage their subjects
        Gate::define('manage-subject', function (User $user, $subject) {
            if ($user->hasRole('admin')) {
                return true;
            }
            
            if ($user->hasRole('professor')) {
                return $user->subjects()->where('id', $subject->id)->exists();
            }
            
            return false;
        });

        // Allow users to manage their own profile
        Gate::define('manage-profile', function (User $user, $profileUser) {
            return $user->id === $profileUser->id || 
                   $user->hasRole('admin') ||
                   ($user->hasRole('parent') && $user->children->contains('id', $profileUser->id));
        });
    }

    /**
     * Extend the authentication guard to handle remember me functionality
     */
    protected function extendAuthGuard(): void
    {
        // Extend the auth guard to handle remember me functionality
        $this->app['auth']->extend('session', function ($app, $name, array $config) {
            $provider = $app['auth']->createUserProvider($config['provider']);
            
            $guard = new \Illuminate\Auth\SessionGuard(
                $name,
                $provider,
                $app['session.store'],
                $app['request'],
                $app['config']['auth.remember_me.expire'] ?? 10080 // Default 7 days
            );
            
            // When using the remember me functionality of the authentication services we
            // will need to be set the encryption instance of the guard, which allows
            // secure, encrypted cookie values to get generated for those cookies.
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }
            
            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }
            
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }
            
            return $guard;
        });
    }
}
