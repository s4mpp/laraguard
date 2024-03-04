<?php

use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Middleware\{Panel, RestrictedArea};
use S4mpp\Laraguard\Controllers\{ChangePasswordController, ModuleController, PasswordRecoveryController, PasswordResetController, RecoveryPasswordController, ResetPasswordController, SignInController, SignOutController, SignUpController, StartController};

$panels = Laraguard::getPanels();

foreach ($panels as $panel) {
    Route::prefix($panel->getPrefix())->middleware([
        Panel::class,
        Illuminate\Session\Middleware\StartSession::class,
        Illuminate\Cookie\Middleware\EncryptCookies::class,
        Illuminate\View\Middleware\ShareErrorsFromSession::class,
        Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    ])->group(function () use ($panel): void {
        Route::get('/', StartController::class)->name($panel->getRouteName('start'));

        Route::prefix('/signin')->controller(SignInController::class)->group(function () use ($panel): void {
            Route::get('/', 'index')->name($panel->getRouteName('login'));
            Route::post('/', 'attempt')->name($panel->getRouteName('attempt_login'));
        });

        if ($panel->hasAutoRegister()) {
            Route::prefix('signup')->controller(SignUpController::class)->group(function () use ($panel): void {
                Route::get('/', 'index')->name($panel->getRouteName('signup'));
                Route::post('/', 'save')->name($panel->getRouteName('create_account'));

                Route::get('/user-registered', 'finish')->name($panel->getRouteName('user_registered'));
            });
        }

        Route::prefix('/password-recovery')->group(function () use ($panel): void {
            Route::controller(PasswordRecoveryController::class)->group(function () use ($panel): void {
                Route::get('/', 'index')->name($panel->getRouteName('recovery_password'));
                Route::post('/', 'sendLink')->name($panel->getRouteName('send_link_password'));
            });

            Route::controller(PasswordResetController::class)->group(function () use ($panel): void {
                Route::get('/change/{token}', 'index')->name($panel->getRouteName('change_password'));
                Route::put('/change', 'storePassword')->name($panel->getRouteName('store_password'));
            });
        });

        Route::middleware(RestrictedArea::class)->group(function () use ($panel): void {
            foreach ($panel->getModules() as $module) {
                Route::prefix($module->getPrefixUrl())->group(function () use ($module, $panel): void {
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

            Route::get('/signout', SignOutController::class)->name($panel->getRouteName('signout'));
        });
    });
}
