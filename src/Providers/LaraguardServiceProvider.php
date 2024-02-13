<?php

namespace S4mpp\Laraguard\Providers;

use S4mpp\Laraguard\Commands\Check;
use S4mpp\Laraguard\Commands\MakeUser;
use Illuminate\Support\ServiceProvider;

final class LaraguardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../views', 'laraguard');

        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'laraguard');

        if($this->app->runningInConsole())
		{
            $this->commands([
                MakeUser::class,
                Check::class
            ]);
        }
    }
}
