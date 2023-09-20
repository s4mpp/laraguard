<?php

namespace S4mpp\Laraguard\Providers;

use Illuminate\Support\ServiceProvider;
use S4mpp\Laraguard\Routes;

class LaraguardServiceProvider extends ServiceProvider 
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../views', 'laraguard');

        if($this->app->environment('testing'))
        {
            $this->loadRoutesFrom(__DIR__.'/../../tests/routes.php');
        }
    }

    public function register()
    {
        $this->app->singleton('routesguard', fn() => new Routes);
    }
}