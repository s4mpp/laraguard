<?php

use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Middleware\{Panel, RestrictedArea};
use S4mpp\Laraguard\Controllers\{ModuleController, PasswordRecoveryController, PasswordResetController,  SignInController, SignOutController, SignUpController, StartController};

Route::aliasMiddleware('restricted-area', RestrictedArea::class);

$panels = Laraguard::getPanels();

foreach ($panels as $panel) {

    $route = ($subdomain = $panel->getSubdomain()) ? Route::domain($subdomain) : Route::prefix($panel->getPrefix());
    
    $route->middleware(['web', Panel::class])->group(function () use ($panel): void {

        Route::get('/', StartController::class)->name($panel->getRouteName('start'));

        Route::prefix('entrar')->controller(SignInController::class)->group(function () use ($panel): void {
            Route::get('/', 'index')->name($panel->getRouteName('login'));
            Route::post('/', 'attempt')->name($panel->getRouteName('attempt_login'));
        });

        if ($panel->hasAutoRegister()) {
            Route::prefix('cadastro')->controller(SignUpController::class)->group(function () use ($panel): void {
                Route::get('/', 'index')->name($panel->getRouteName('signup'));
                Route::post('/', 'save')->name($panel->getRouteName('create_account'));

                Route::get('/finalizado', 'finish')->name($panel->getRouteName('user_registered'));
            });
        }

        Route::prefix('recuperacao-de-senha')->controller(PasswordRecoveryController::class)->group(function () use ($panel): void {
            Route::get('/', 'index')->name($panel->getRouteName('recovery_password'));
            Route::post('/', 'sendLink')->name($panel->getRouteName('send_link_password'));
        });

        Route::prefix('alterar-senha')->controller(PasswordResetController::class)->group(function () use ($panel): void {
            Route::get('/{token}', 'index')->name($panel->getRouteName('change_password'));
            Route::put('/', 'storePassword')->name($panel->getRouteName('store_password'));
        });

        Route::middleware('restricted-area:'.$panel->getGuardName())->group(function () use ($panel): void {
            foreach ($panel->getModules() as $module) {
                                
                Route::prefix($module->getPrefixUrl())
                    ->middleware($module->getMiddlewares())
                    ->group(function () use ($module, $panel): void {
                    $controller = $module->getController();

                    foreach ($module->getPages() as $i => $page) {
                        if ($page->getIsIndex()) {
                            $action = $controller ?? ModuleController::class;
                        } else {
                            $action_controller = $page->getAction();

                            $action = ($controller && $action_controller) ? [$controller, $action_controller] : ModuleController::class;
                        }

                        $route_page = Route::{$page->getMethod()}($page->getUri(), $action)->name($panel->getRouteName($module->getSlug(), $page->getSlug()));

                        if ($middlewares = $page->getMiddlewares()) {
                            $route_page->middleware($middlewares);
                        }
                    }
                });
            }

            Route::get('sair', SignOutController::class)->name($panel->getRouteName('signout'));
        });
    });
}
