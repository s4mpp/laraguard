<?php

namespace Workbench\App\Providers;

use S4mpp\Laraguard\Laraguard;
use Workbench\App\Models\Customer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

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
        
        Config::set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'customers'
        ]);
        
        Config::set('auth.providers.customers', [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ]);
    }
}