<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use S4mpp\Laraguard\Controllers\LaraguardController;

class SignOutController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $panel = Laraguard::getPanel(Panel::current());

        $panel->logout();
    
        return to_route($panel->getRouteName('login'))->with('message', __('laraguard::login.logout_successfull'));
    }
}
