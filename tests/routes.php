<?php

use S4mpp\Laraguard\Routes;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Tests\TestController;

Route::prefix('test')->group(function()
{
	Route::controller(TestController::class)->group(function()
	{
		Routes::identifier('test')->authGroup();
		
		Routes::identifier('test')->forgotAndRecoveryPasswordGroup();
	});
});
