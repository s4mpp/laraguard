<?php

namespace Workbench\App\Providers;

use S4mpp\Laraguard\Laraguard;
use Workbench\App\Models\Customer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Models\User;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Laraguard::guard('Restricted area', 'restricted-area'); // web
        
        Laraguard::guard('My account', 'customer-area', 'customer');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // dump(env('MAIL_HOST'));

        Config::set('auth.providers.customers', [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ]);

        Config::set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);

        Config::set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'customers'
        ]);

        Config::set('auth.passwords.customer', [
            'provider' => 'customers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 0,
        ]);
        Config::set('auth.passwords.web', [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 0,
        ]);
    }
}