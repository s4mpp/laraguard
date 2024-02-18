<?php

namespace Workbench\App\Providers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Models\{Customer, User};
use Workbench\App\Controllers\{ExtractController, TeamController, WithdrawalController};

final class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $restricted_area = Laraguard::panel('Restricted area', 'restricted-area'); // web

        $restricted_area->addModule('Dashboard', 'home')->addIndex();

        $restricted_area->addModule('No Index', 'no-index');

        $events = $restricted_area->addModule('Events', 'events');
        $events->addPage('List', 'list')->isIndex();

        $finances_module = $restricted_area->addModule('Finances', 'finances')->addIndex();
        $finances_module->addPage('Report');

        $restricted_area->addSection('Section 1', 'section-1', [
            $restricted_area->addModule('Orders', 'orders')->addIndex('index-example'),

            $restricted_area->addModule('Team')->controller(TeamController::class)->addIndex(),
        ]);

        $restricted_area->addModule('Extract', 'extract')->controller(ExtractController::class)->addIndex();
        $restricted_area->addModule('Withdrawal')->controller(WithdrawalController::class)->addIndex();

        $my_account = Laraguard::panel('My account', 'customer-area', 'customer')->allowAutoRegister();

        $my_account->layout()
            ->setHtmlFile('custom.html')
            ->setAuthFile('custom.auth')
            ->setLayoutFile('custom.layout');

        Laraguard::panel('Guest area', 'guest-area', 'guest')->allowAutoRegister(); // Force invalid for tests
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

        Config::set('auth.providers.guests', [
            'driver' => 'eloquent',
            'model' => null,
        ]);

        Config::set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'customers',
        ]);

        Config::set('auth.guards.guest', [
            'driver' => 'session',
            'provider' => 'guests',
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
