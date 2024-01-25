<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Controllers\LaraguardController;

class StartController extends Controller
{
    public function __invoke()
    {
        $panel = Laraguard::currentPanel();

        if(!$panel->checkIfIsUserIsLogged())
        {
            return to_route($panel->getRouteName('login'));
        }
        
        return to_route($panel->getRouteName('my-account'));
    }
}
