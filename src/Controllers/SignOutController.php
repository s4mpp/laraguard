<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Controllers\LaraguardController;

class SignOutController extends Controller
{
    public function __invoke()
    {
        $panel = Laraguard::currentPanel();

        if(!$panel->logout())
        {
            return redirect()->back();
        }
        
        return to_route($panel->getRouteName('login'))->withMessage(__('laraguard::login.logout_successfull'));
    }
}
