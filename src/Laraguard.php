<?php

namespace S4mpp\Laraguard;

use Closure;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Controllers\LoginController;
use S4mpp\Laraguard\Controllers\PasswordRecoveryController;

class Laraguard
{
	private static $guards = [];

	public static function guard(string $title, string $slug, string $guard = 'web')
	{		
		$laraguard = new Guard($title, $slug, $guard);
		
		self::$guards[$guard] = $laraguard;

		return $guard;
	}

	public static function getGuards(): array
	{
		return self::$guards;
	}

	public static function getCurrentGuard(string $route = null): ?Guard
    {
        $path_steps = explode('.', $route);

		$guard_name = $path_steps[1] ?? null;

		if(!$guard_name || !$current_guard = self::getGuard($guard_name))
		{
			return null;
		}

		return $current_guard;
    }

	public static function getGuard(string $guard_name): ?Guard
	{
		return self::$guards[$guard_name] ?? null;
	}

	public static function routes(string $guard_name = 'web', Closure $routes = null)
	{
		$guard = self::getGuard($guard_name);

		if(!$guard)
		{
			return false;
		}

		Route::middleware('web')->prefix($guard->getPrefix())->group(function() use ($routes, $guard)
		{
			Route::prefix('/signin')->controller(LoginController::class)->group(function() use ($guard)
			{
				Route::get('/', 'index')->name($guard->getRouteName('login'));
				Route::post('/', 'attempt')->name($guard->getRouteName('attempt_login'));
			});

			Route::prefix('/password-recovery')->controller(PasswordRecoveryController::class)->group(function() use ($guard)
			{
				Route::get('/', 'index')->name($guard->getRouteName('recovery_password'));
				Route::post('/', 'sendLink')->name($guard->getRouteName('send_link_password'));
				
				Route::get('/change/{token}', 'changePassword')->name($guard->getRouteName('change_password'));
				Route::put('/change', 'storePassword')->name($guard->getRouteName('store_password'));
			});

			if(is_callable($routes))
			{
				Route::middleware('laraguard:'.$guard->getGuardName(), 'auth:'.$guard->getGuardName())->group(function() use ($routes, $guard)
				{
					return call_user_func($routes, $guard);
				});
			}
			
			Route::get('/signout', [LoginController::class, 'signout'])->name($guard->getRouteName('signout'));
		});
	}
}