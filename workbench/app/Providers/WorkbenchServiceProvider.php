<?php

namespace Workbench\App\Providers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Models\{Customer, User};
use Workbench\App\Controllers\InvokeLayoutController;
use Workbench\App\Controllers\InvokeLayoutDefaultController;
use Workbench\App\Controllers\{ExtractController, TeamController, WithdrawalController};
use Workbench\App\MIddleware\ExampleMiddleware;

final class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $restricted_area = Laraguard::panel('Restricted area', 'restricted-area'); // web

        $restricted_area->addModule('Dashboard', 'home')->starter()->addIndex();

        $restricted_area->addModule('No Index', 'no-index');

        $module = $restricted_area->addModule('Module A', 'module');
        $module->addPage('Page', 'page')->isIndex();

        $module = $restricted_area->addModule('Module B', 'moduleb')->middleware(ExampleMiddleware::class)->addIndex();

        $finances_module = $restricted_area->addModule('Module 2', 'module-2')->addIndex();
        $finances_module->addPage('Page');
        $finances_module->addPage('Page with middleware')->middleware(ExampleMiddleware::class);

        $restricted_area->addSection('Section', 'section', [
            $restricted_area->addModule('Subsection 1', 'subsection-1')->addIndex('index-example'),

            $restricted_area->addModule('Subsection 2', 'subsection-2')->addIndex(),
        ]);

        $restricted_area->addModule('Invoke layout default')->controller(InvokeLayoutDefaultController::class)->addIndex();
        $restricted_area->addModule('Invoke layout')->controller(InvokeLayoutController::class)->addIndex();

        $my_account = Laraguard::panel('Customer area', 'customer-area', 'customer')->allowAutoRegister();

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
