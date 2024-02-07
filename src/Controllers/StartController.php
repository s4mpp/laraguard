<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Http\RedirectResponse;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Controllers\LaraguardController;

class StartController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $panel = Laraguard::getPanel(Panel::current());

        if(!$panel->checkIfIsUserIsLogged())
        {
            return to_route($panel->getRouteName('login'));
        }
        
        return to_route($panel->getRouteName('my-account', 'index'));
    }
}
