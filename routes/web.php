<?php

use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Middleware\{Panel, RestrictedArea};
use S4mpp\Laraguard\Controllers\{ModuleController, PasswordRecoveryController, PasswordResetController,  SignInController, SignOutController, SignUpController, StartController};

Route::aliasMiddleware('restricted-area', RestrictedArea::class);

$uris = [
    'signin' => config('laraguard.routes.signin', 'signin'),
    'signup' => [
        'index' => config('laraguard.routes.signup', 'signup'),
        'user-registered' => config('laraguard.routes.signup.user-registered', 'user-registered'),
    ],
    'password-recovery' => [
        'index' => config('laraguard.routes.password-recovery.index', 'password-recovery'),
        'change' => config('laraguard.routes.password-recovery.change', 'change'),
    ],
    'signout' => config('laraguard.routes.signout', 'signout')
];

$panels = Laraguard::getPanels();

foreach ($panels as $panel) {

    $route = ($subdomain = $panel->getSubdomain()) ? Route::domain($subdomain) : Route::prefix($panel->getPrefix());
    
    $route->middleware(['web', Panel::class])->group(function () use ($panel, $uris): void {

        Route::get('/', StartController::class)->name($panel->getRouteName('start'));

        Route::prefix($uris['signin'])->controller(SignInController::class)->group(function () use ($panel): void {
            Route::get('/', 'index')->name($panel->getRouteName('login'));
            Route::post('/', 'attempt')->name($panel->getRouteName('attempt_login'));
        });

        if ($panel->hasAutoRegister()) {
            Route::prefix($uris['signup']['index'])->controller(SignUpController::class)->group(function () use ($panel, $uris): void {
                Route::get('/', 'index')->name($panel->getRouteName('signup'));
                Route::post('/', 'save')->name($panel->getRouteName('create_account'));

                Route::get($uris['signup']['user-registered'], 'finish')->name($panel->getRouteName('user_registered'));
            });
        }

        Route::prefix($uris['password-recovery']['index'])->group(function () use ($panel, $uris): void {
            Route::controller(PasswordRecoveryController::class)->group(function () use ($panel, $uris): void {
                Route::get('/', 'index')->name($panel->getRouteName('recovery_password'));
                Route::post('/', 'sendLink')->name($panel->getRouteName('send_link_password'));
            });

            Route::controller(PasswordResetController::class)->group(function () use ($panel, $uris): void {
                Route::get($uris['password-recovery']['change'].'/{token}', 'index')->name($panel->getRouteName('change_password'));
                Route::put($uris['password-recovery']['change'], 'storePassword')->name($panel->getRouteName('store_password'));
            });
        });

        Route::middleware('restricted-area:'.$panel->getGuardName())->group(function () use ($panel, $uris): void {
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

            Route::get($uris['signout'], SignOutController::class)->name($panel->getRouteName('signout'));
        });
    });
}
