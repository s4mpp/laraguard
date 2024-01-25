<?php

namespace S4mpp\Laraguard\Providers;

use Illuminate\Support\ServiceProvider;
use S4mpp\Laraguard\Middleware\Laraguard;
use S4mpp\Laraguard\Middleware\RestrictedArea;

class LaraguardServiceProvider extends ServiceProvider 
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../views', 'laraguard');

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'laraguard');

        // app('router')->aliasMiddleware('laraguard', Laraguard::class);
        // app('router')->aliasMiddleware('restricted-area', RestrictedArea::class);
    }
}