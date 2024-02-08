<?php

use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Middleware\RestrictedArea;
use S4mpp\Laraguard\Controllers\StartController;
use S4mpp\Laraguard\Controllers\ModuleController;
use S4mpp\Laraguard\Controllers\SignInController;
use S4mpp\Laraguard\Controllers\SignUpController;
use S4mpp\Laraguard\Controllers\SignOutController;
use S4mpp\Laraguard\Controllers\ChangePasswordController;
use S4mpp\Laraguard\Controllers\RecoveryPasswordController;
use S4mpp\Laraguard\Middleware\Panel;

$panels = Laraguard::getPanels();

foreach($panels as $panel)
{
	Route::prefix($panel->getPrefix())->middleware([
		Panel::class,
		\Illuminate\Session\Middleware\StartSession::class,
		\Illuminate\View\Middleware\ShareErrorsFromSession::class
	])->group(function() use ($panel)
	{
		Route::get('/', StartController::class)->name($panel->getRouteName('start'));
		
		Route::prefix('/signin')->controller(SignInController::class)->group(function() use ($panel)
		{
			Route::get('/', 'index')->name($panel->getRouteName('login'));
			Route::post('/', 'attempt')->name($panel->getRouteName('attempt_login'));
		});

		if($panel->hasAutoRegister())
		{
			Route::prefix('signup')->controller(SignUpController::class)->group(function() use ($panel)
			{
				Route::get('/', 'index')->name($panel->getRouteName('signup'));
				Route::post('/', 'save')->name($panel->getRouteName('create_account'));
				
				Route::get('/user-registered', 'finish')->name($panel->getRouteName('user_registered'));
			});
		}

		Route::prefix('/password-recovery')->group(function() use ($panel)
		{
			Route::controller(RecoveryPasswordController::class)->group(function() use ($panel)
			{
				Route::get('/', 'index')->name($panel->getRouteName('recovery_password'));
				Route::post('/', 'sendLink')->name($panel->getRouteName('send_link_password'));
			});

			Route::controller(ChangePasswordController::class)->group(function() use ($panel)
			{
				Route::get('/change/{token}', 'index')->name($panel->getRouteName('change_password'));
				Route::put('/change', 'storePassword')->name($panel->getRouteName('store_password'));
			});
		});

		Route::middleware(RestrictedArea::class)->group(function() use ($panel)
		{
			foreach($panel->getModules() as $module)
			{
				Route::prefix($module->getSlug())->group(function() use ($module, $panel)
				{
					$controller = $module->getController();
					
					foreach($module->getPages() as $page)
					{
						if($page->isIndex())
						{
							$action = $controller ?? ModuleController::class;
						}
						else
						{
							$action_controller = $page->getAction();

							$action = ($controller && $action_controller) ? [$controller, $action_controller] : ModuleController::class;
						}

						$method = $page->getMethod();

						Route::{$method}($page->getUri(), $action)->name($panel->getRouteName($module->getSlug(), $page->getSlug()));
					}
				});
			}
				
			Route::get('/signout', SignOutController::class)->name($panel->getRouteName('signout'));
		});
	});
}