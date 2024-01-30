<?php

namespace S4mpp\Laraguard;

use Closure;
use S4mpp\Laraguard\Panel;
use S4mpp\Laraguard\Middleware\Page;
use Illuminate\Support\Facades\Route;
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
	private static $guards = [];

	public static function panel(string $title, string $prefix = '', string $guard = 'web'): Panel
	{
		$panel = new Panel($title, $prefix, $guard);

		$panel->addPage('My account', 'laraguard::my-account', 'my-account')->hideInMenu();
		
		self::$guards[$guard] = $panel;

		return $panel;
	}

	public static function getGuards(): array
	{
		return self::$guards;
	}

	public static function getCurrentPanelByRoute(string $route = null): ?Panel
    {
        $path_steps = explode('.', $route);

		$guard_name = $path_steps[1] ?? null;

		return self::getPanel($guard_name ?? '');
    }

	public static function getPanel(string $guard_name): ?Panel
	{
		return self::$guards[$guard_name] ?? null;
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
	
		Route::prefix($panel->getPrefix())->middleware(PanelMiddleware::class)->group(function() use ($routes, $panel)
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
				Route::middleware('web')->group(function() use ($routes, $panel)
				{
					return (is_callable($routes)) ? call_user_func($routes, $panel) : null;
				});

				Route::middleware(Page::class)->group(function() use ($panel)
				{
					foreach($panel->getPages() as $page)
					{
						Route::get($page->getSlug(), $page->getAction())->name($panel->getRouteName($page->getSlug()));
					}
				});
					
				Route::get('/signout', SignOutController::class)->name($panel->getRouteName('signout'));
			});
		});
	}
}