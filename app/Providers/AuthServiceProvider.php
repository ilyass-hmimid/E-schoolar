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
}
