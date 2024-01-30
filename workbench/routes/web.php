<?php

use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Route;
use S4mpp\Laraguard\Panel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test', function () {

    dump(Laraguard::getGuards());

    dump(app('config'));

    return 'welcome';
});

Laraguard::routes('customer', function(Panel $panel)
{
    Route::get('home', function() use ($panel)
    {
        return 'Logged';
    });
});

Laraguard::routes('web', function()
{
    Route::get('home', function()
    {
        return 'Logged';
    });
}); // Restricted area

Laraguard::routes('invalid-guard'); // force invalid
