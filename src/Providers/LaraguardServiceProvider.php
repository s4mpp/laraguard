<?php

namespace S4mpp\Laraguard\Providers;

use Illuminate\Support\ServiceProvider;
use S4mpp\Laraguard\Routes;

class LaraguardServiceProvider extends ServiceProvider 
{
    public function boot()
    {
        // $this->loadViewsFrom(__DIR__.'/../../views', 'laraguard');

        // if($this->app->environment('testing'))
        // {
        //     $this->loadRoutesFrom(__DIR__.'/../../tests/routes.php');
        // }

        // if($this->app->runningInConsole())
		// {
        //     $this->publishes([
        //         __DIR__.'/../../stubs/migration_add_auth_fields.stub' => database_path('migrations/'.date('Y_m_d_His').'_add_2fa_fields_on_users.php'), 
		// 	], 'laraguard-2fa-migration');
        // }
    }

    public function register()
    {
        // $this->app->singleton('routesguard', fn() => new Routes);
    }
}