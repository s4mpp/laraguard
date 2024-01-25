<?php

namespace Workbench\App\Providers;

use S4mpp\Laraguard\Laraguard;
use Workbench\App\Models\User;
use Workbench\App\Models\Customer;
use S4mpp\Laraguard\Navigation\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Controllers\TeamController;
use Workbench\App\Controllers\ExtractController;
use Workbench\App\Controllers\WithdrawalController;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $restricted_area = Laraguard::guard('Restricted area', 'restricted-area'); // web
        
        $restricted_area->addPage('Dashboard', null, 'home');
        $restricted_area->addPage('Orders', 'orders');
        $restricted_area->addPage('Team')->controller(TeamController::class);
        $restricted_area->addPage('Extract', 'extract')->controller(ExtractController::class);
        $restricted_area->addPage('Withdrawal')->controller(WithdrawalController::class);
        
        
        Laraguard::guard('My account', 'customer-area', 'customer');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
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