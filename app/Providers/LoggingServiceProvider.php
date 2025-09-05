<?php

namespace App\Providers;

use App\Http\Middleware\LogAction;
use Illuminate\Support\ServiceProvider;

class LoggingServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any bindings or services
    }

    public function boot()
    {
        // Register the middleware globally
        $this->app['router']->aliasMiddleware('log.actions', LogAction::class);
    }
}
