<?php

namespace S4mpp\Laraguard;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

class Routes extends Facade
{
	public static ?string $identifier = null;
	
	public static function identifier(string $identifier = null)
	{
		throw_if(!$identifier, 'Identifier is required');

		self::$identifier = $identifier ? '_'.$identifier : null;
		
		return new self();
	}

	public static function authGroup(bool $has_2fa = false)
	{
		Route::middleware('web')->group(function() use ($has_2fa)
		{
			Route::get('/entrar', 'login')->name(self::login());
			Route::post('/entrar', 'attemptLogin')->name(self::attemptLogin());

			if($has_2fa)
			{
				Route::get('/2fa/{code}', 'login2fa')->name(self::login2fa());
				Route::post('/2fa/{code}', 'attemptLogin2fa')->name(self::attemptLogin2fa());
			}
			
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

	public static function login2fa()
	{
		return 'login_2fa_'.self::$identifier;
	}

	public static function logout()
	{
		return 'logout'.self::$identifier;
	}

	public static function attemptLogin()
	{
		return 'attempt_login'.self::$identifier;
	}

	public static function attemptLogin2fa()
	{
		return 'attempt_login_2fa'.self::$identifier;
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