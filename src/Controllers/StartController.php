<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use S4mpp\Laraguard\Controllers\BaseController;
use S4mpp\Laraguard\Controllers\LaraguardController;

class StartController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $panel = $request->get('laraguard_panel');

        if(!$panel->checkIfIsUserIsLogged())
        {
            return to_route($panel->getRouteName('login'));
        }
        
        return to_route($panel->getRouteName('my-account', 'index'));
    }
}
