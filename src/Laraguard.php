<?php

namespace S4mpp\Laraguard;

use Closure;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Controllers\LoginController;
use S4mpp\Laraguard\Controllers\PasswordRecoveryController;

class Laraguard
{
	private static $guards = [];

	public static function guard(string $title, string $slug, string $guard)
	{		
		$laraguard = new Guard($title, $slug, $guard);
		
		self::$guards[$guard] = $laraguard;

		return $guard;
	}

	public static function getGuards(): array
	{
		return self::$guards;
	}

	public static function getCurrentGuard(): Guard
    {
        $path_steps = explode('.', request()->route()->action['as']);

		$guard_name = $path_steps[1] ?? null;

		$current_guard = self::getGuard($guard_name);

		if(!$guard_name || !$current_guard)
		{
			abort(404);
		}

		return $current_guard;
    }

	public static function getGuard(string $guard_name): ?Guard
	{
		return self::$guards[$guard_name] ?? null;
	}

	public static function routes(Closure $routes, string $guard_name = 'web')
	{
		$guard = self::getGuard($guard_name);

		Route::prefix($guard->getPrefix())->group(function() use ($routes, $guard)
		{
			Route::prefix('/entrar')->controller(LoginController::class)->group(function() use ($guard)
			{
				Route::get('/', 'index')->name($guard->getRouteName('login'));
				Route::post('/', 'attemptLogin')->name($guard->getRouteName('attempt_login'));
			});

			Route::prefix('/recuperar-senha')->controller(PasswordRecoveryController::class)->group(function() use ($guard)
			{
				Route::get('/', 'index')->name($guard->getRouteName('recovery_password'));
				Route::post('/', 'sendLink')->name($guard->getRouteName('send_link_password'));
				
				Route::get('/alterar/{token}', 'changePassword')->name($guard->getRouteName('change_password'));
				Route::put('/salvar-nova-senha', 'storePassword')->name($guard->getRouteName('store_password'));
			});
			
			Route::middleware('laraguard:'.$guard->getGuardName())->group(function() use ($routes, $guard)
			{
				return call_user_func($routes, $guard);
			});

			Route::get('/sair', [LoginController::class, 'signout'])->name($guard->getRouteName('signout'));
		});
	}
}