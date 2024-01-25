<?php

namespace S4mpp\Laraguard;

use Closure;
use S4mpp\Laraguard\Middleware\Page;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Middleware\Panel;
use S4mpp\Laraguard\Middleware\RestrictedArea;
use S4mpp\Laraguard\Controllers\SignInController;
use S4mpp\Laraguard\Controllers\SignUpController;
use S4mpp\Laraguard\Controllers\SignOutController;
use S4mpp\Laraguard\Controllers\MyAccountController;
use S4mpp\Laraguard\Controllers\ChangePasswordController;
use S4mpp\Laraguard\Controllers\PasswordRecoveryController;
use S4mpp\Laraguard\Controllers\RecoveryPasswordController;
use S4mpp\Laraguard\Controllers\RecoverPasswordChangeController;
use S4mpp\Laraguard\Controllers\RecoveryPasswordSolicitationController;

class Laraguard
{
	private static $guards = [];

	public static function guard(string $title, string $slug, string $guard = 'web'): Guard
	{		
		$panel = new Guard($title, $slug, $guard);

		$panel->addPage('My account', 'laraguard::my-account', 'my-account')->hideInMenu();
		
		self::$guards[$guard] = $panel;

		return $panel;
	}

	public static function getGuards(): array
	{
		return self::$guards;
	}

	public static function getCurrentGuardByRoute(string $route = null): ?Guard
    {
        $path_steps = explode('.', $route);

		$guard_name = $path_steps[1] ?? null;

		return self::getGuard($guard_name ?? '');
    }

	public static function getGuard(string $guard_name): ?Guard
	{
		return self::$guards[$guard_name] ?? null;
	}

	public static function currentPanel()
	{
		return self::getGuard(request()->get('laraguard_panel'));
	}

	public static function layout(string $file = null, array $data = [])
	{
		return self::currentPanel()->getLayout($file, $data);
	}

	











	public static function routes(string $guard_name = 'web', Closure $routes = null)
	{
		$guard = self::getGuard($guard_name);

		if(!$guard)
		{
			return false;
		}
	
		Route::prefix($guard->getPrefix())->middleware(Panel::class)->group(function() use ($routes, $guard)
		{
			Route::redirect('/', $guard->getPrefix().'/signin');
			
			Route::prefix('/signin')->controller(SignInController::class)->group(function() use ($guard)
			{
				Route::get('/', 'index')->name($guard->getRouteName('login'));
				Route::post('/', 'attempt')->name($guard->getRouteName('attempt_login'));
			});

			if($guard->hasAutoRegister())
			{
				Route::prefix('signup')->controller(SignUpController::class)->group(function() use ($guard)
				{
					Route::get('/', 'index')->name($guard->getRouteName('signup'));
					Route::post('/createAccount', 'save')->name($guard->getRouteName('create_account'));
				});
			}

			Route::prefix('/password-recovery')->group(function() use ($guard)
			{
				Route::controller(RecoveryPasswordController::class)->group(function() use ($guard)
				{
					Route::get('/', 'index')->name($guard->getRouteName('recovery_password'));
					Route::post('/', 'sendLink')->name($guard->getRouteName('send_link_password'));
				});

				Route::controller(ChangePasswordController::class)->group(function() use ($guard)
				{
					Route::get('/change/{token}', 'index')->name($guard->getRouteName('change_password'));
					Route::put('/change', 'storePassword')->name($guard->getRouteName('store_password'));
				});
			});

			Route::middleware(RestrictedArea::class)->group(function() use ($routes, $guard)
			{
				Route::middleware('web')->group(function() use ($routes, $guard)
				{
					return (is_callable($routes)) ? call_user_func($routes, $guard) : null;
				});

				Route::middleware(Page::class)->group(function() use ($guard)
				{
					foreach($guard->getPages() as $page)
					{
						Route::get($page->getSlug(), $page->getAction())->name($guard->getRouteName($page->getSlug()));
					}
				});
	
				// Route::get('/my-account', MyAccountController::class)->name($guard->getRouteName('my_account'));
				
				Route::get('/signout', SignOutController::class)->name($guard->getRouteName('signout'));
			});
		});
	}
}