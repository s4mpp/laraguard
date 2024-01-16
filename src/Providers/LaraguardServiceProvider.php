<?php

namespace S4mpp\Laraguard\Providers;

use Illuminate\Support\ServiceProvider;
use S4mpp\Laraguard\Middleware\Laraguard;

class LaraguardServiceProvider extends ServiceProvider 
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../views', 'laraguard');

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'laraguard');

        app('router')->aliasMiddleware('laraguard', Laraguard::class);
    }
}