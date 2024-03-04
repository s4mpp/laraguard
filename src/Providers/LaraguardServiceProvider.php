<?php

namespace S4mpp\Laraguard\Providers;

use Illuminate\Support\ServiceProvider;
use S4mpp\Laraguard\Commands\{Check, MakeUser};
use S4mpp\Laraguard\Middleware\RestrictedArea;

/**
 * @codeCoverageIgnore
 */
final class LaraguardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../views', 'laraguard');

        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'laraguard');

        app('router')->aliasMiddleware('restricted-area', RestrictedArea::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeUser::class,
                Check::class,
            ]);

            $this->publishes([
                __DIR__.'/../../style/dist.css' => public_path('vendor/laraguard/style.css'),
            ], 'laraguard-assets');
        }
    }
}
