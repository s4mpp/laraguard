<?php

namespace Workbench\App\Providers;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Models\{Customer, User};
use Workbench\App\MIddleware\ExampleMiddleware;
use Workbench\App\Controllers\InvokeLayoutController;
use Workbench\App\Controllers\InvokeLayoutDefaultController;
use Workbench\App\Controllers\{ExtractController, TeamController, WithdrawalController};

final class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $restricted_area = Laraguard::panel('Área Restrita', 'area-restrita'); // web

        $restricted_area->createModule('Dashboard', 'home')->starter()->addIndex();
        
        $restricted_area->createModule('Dashboard 2', 'home-2')->addIndex()->hideInMenu(fn() => true);

        $restricted_area->createModule('No Index', 'no-index');

        $module = $restricted_area->createModule('Module A', 'module');
        $module->createPage('Page', 'page')->isIndex();

        $module = $restricted_area->createModule('Module B', 'moduleb')->middleware(ExampleMiddleware::class)->addIndex();

        $finances_module = $restricted_area->createModule('Module 2', 'module-2')->addIndex();
        $finances_module->createPage('Page');
        $finances_module->createPage('Page with middleware')->middleware(ExampleMiddleware::class);

        $restricted_area->addSection('Section', 'section', [
            $restricted_area->createModule('Subsection 1', 'subsection-1')->addIndex('index-example'),

            $subsection2 = $restricted_area->createModule('Subsection 2', 'subsection-2')->addIndex(),
        ]);
        
        $subsection2->createPage('Page of subsection 2', 'page-subsection-2');

        $restricted_area->createModule('Invoke layout default')->controller(InvokeLayoutDefaultController::class)->addIndex();
        $restricted_area->createModule('Invoke layout')->controller(InvokeLayoutController::class)->addIndex();

        $restricted_area->addSection('Minha Conta', 'minha-conta', [
            $restricted_area->addModule(Module::changePersonalData()),
            $restricted_area->addModule(Module::changePassword()),
        ]);

        $my_account = Laraguard::panel('Área do cliente', 'area-do-cliente', 'customer')->allowAutoRegister();

        $my_account->createModule('Dashboard', 'dashboard')->starter()->addIndex();

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
