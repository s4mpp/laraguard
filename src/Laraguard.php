<?php

namespace S4mpp\Laraguard;

use Closure;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Middleware\Page;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Middleware\Module;
use S4mpp\Laraguard\Middleware\RestrictedArea;
use S4mpp\Laraguard\Controllers\StartController;
use S4mpp\Laraguard\Controllers\SignInController;
use S4mpp\Laraguard\Controllers\SignUpController;
use S4mpp\Laraguard\Controllers\SignOutController;
use S4mpp\Laraguard\Controllers\MyAccountController;
use S4mpp\Laraguard\Middleware\Panel as PanelMiddleware;
use S4mpp\Laraguard\Controllers\ChangePasswordController;
use S4mpp\Laraguard\Controllers\PasswordRecoveryController;
use S4mpp\Laraguard\Controllers\RecoveryPasswordController;
use S4mpp\Laraguard\Controllers\RecoverPasswordChangeController;
use S4mpp\Laraguard\Controllers\RecoveryPasswordSolicitationController;

class Laraguard
{
	private static $panels = [];

	public static function panel(string $title, string $prefix = '', string $guard = 'web'): Panel
	{
		$panel = new Panel($title, $prefix, $guard);

		$panel->addModule('My account', 'my-account')->hideInMenu();
		
		self::$panels[$guard] = $panel;

		return $panel;
	}

	public static function getGuards(): array
	{
		return self::$panels;
	}

	/**
	 * @todo move to Utils
	 */
	public static function getCurrentPanelByRoute(string $route = null): ?Panel
    {
        $path_steps = explode('.', $route);

		$guard_name = $path_steps[1] ?? null;

		return self::$panels[$guard_name] ?? null;
    }

	public static function getPanel(string $guard_name): ?Panel
	{
		return self::$panels[$guard_name] ?? null;
	}

	public static function currentPanel()
	{
		return self::getPanel(request()->get('laraguard_panel'));
	}

	public static function layout(string $file = null, array $data = [])
	{
		return self::currentPanel()->getLayout($file, $data);
	}

	











	public static function routes(string $guard_name = 'web', Closure $routes = null)
	{
		$panel = self::getPanel($guard_name);

		if(!$panel)
		{
			return false;
		}
	
		Route::prefix($panel->getPrefix())->middleware([
			PanelMiddleware::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
		])->group(function() use ($routes, $panel)
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

			Route::middleware(RestrictedArea::class)->group(function() use ($routes, $panel)
			{
				// Route::middleware('web')->group(function() use ($routes, $panel)
				// {
				// 	return (is_callable($routes)) ? call_user_func($routes, $panel) : null;
				// });

				foreach($panel->getModules() as $module)
				{
					Route::prefix($module->getSlug())->group(function() use ($module, $panel)
					{
						$controller = $module->getController();
						
						foreach($module->getPages() as $page)
						{
							$action = ($method = $page->getMethod()) ? [$controller, $method] : $controller;

							Route::middleware(Page::class)->get($page->getSlug(), $action)->name($panel->getRouteName($module->getSlug(), $page->getSlug()));
						}
					});
				}
					
				Route::get('/signout', SignOutController::class)->name($panel->getRouteName('signout'));
			});
		});
	}
}