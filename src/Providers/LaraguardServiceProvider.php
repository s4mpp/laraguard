<?php

namespace S4mpp\Laraguard\Providers;

use Illuminate\Support\ServiceProvider;

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
}