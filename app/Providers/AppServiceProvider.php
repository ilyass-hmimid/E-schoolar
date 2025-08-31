<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Utiliser le template de pagination Bootstrap 5
        Paginator::useBootstrapFive();
        
        // Définir le nombre d'éléments par page par défaut
        Paginator::defaultView('vendor.pagination.bootstrap-5');
    }
}
