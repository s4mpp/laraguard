<?php

namespace S4mpp\Laraguard;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

class Routes extends Facade
{
	public static ?string $identifier = null;
	
	public static function identifier(string $identifier = null)
	{
		self::$identifier = $identifier ? '_'.$identifier : null;

		return new self();
	}

	public static function authGroup()
	{
		Route::middleware('web')->group(function()
		{
			Route::get('/entrar', 'login')->name(self::login());
			
			Route::post('/entrar', 'attemptLogin')->name(self::attemptLogin());
			
			Route::get('/sair', 'logout')->name(self::logout());
		});

		return new self();
	}

	public static function forgotAndRecoveryPasswordGroup()
	{
		Route::middleware('web')->prefix('/recuperar-senha')->group(function()
		{
			Route::get('/', 'forgotPassword')->name(self::forgotPassword());
			Route::post('/', 'recoveryPassword')->name(self::recoveryPassword());
			
			Route::prefix('/alterar/{token_password_recovery}')->group(function()
			{
				Route::get('/', 'changePasswordRecovery')->name(self::changePasswordRecovery());
				Route::put('/', 'storePasswordRecovery')->name(self::storePasswordRecovery());
			});
		});

		return new self();
	}

	public static function login()
	{
		return 'login'.self::$identifier;
	}

	public static function logout()
	{
		return 'logout'.self::$identifier;
	}

	public static function attemptLogin()
	{
		return 'attempt_login'.self::$identifier;
	}

	public static function forgotPassword()
	{
		return 'forgot_password'.self::$identifier;
	}

	public static function recoveryPassword()
	{
		return 'recovery_password'.self::$identifier;
	}

	public static function changePasswordRecovery()
	{
		return 'change_password_recovery'.self::$identifier;
	}

	public static function storePasswordRecovery()
	{
		return 'store_password_recovery'.self::$identifier;
	}
}