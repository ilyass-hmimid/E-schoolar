<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';
    public const LOGIN = '/login';
    
    /**
     * The path to redirect to after authentication based on user role.
     *
     * @var array
     */
    public const ROLE_HOMES = [
        'admin' => '/admin/dashboard',
        'professeur' => '/professeur/dashboard',
        'eleve' => '/eleve/dashboard',
        'assistant' => '/assistant/dashboard',
    ];
    
    /**
     * Get the path the user should be redirected to based on their role.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    public static function getHomeForUser($user)
    {
        if (!$user) {
            return self::HOME;
        }
        
        // Check user roles in order of priority
        if ($user->hasRole('admin')) {
            return self::ROLE_HOMES['admin'];
        }
        
        if ($user->hasRole('professeur')) {
            return self::ROLE_HOMES['professeur'];
        }
        
        if ($user->hasRole('eleve')) {
            return self::ROLE_HOMES['eleve'];
        }
        
        if ($user->hasRole('assistant')) {
            return self::ROLE_HOMES['assistant'];
        }
        
        return self::HOME;
    }


    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(function () {
                    require base_path('routes/web.php');
                    require base_path('routes/admin_users.php');
                    require base_path('routes/admin.php');
                });
        });
    }
}
