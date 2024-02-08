<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use S4mpp\Laraguard\Controllers\BaseController;
use S4mpp\Laraguard\Controllers\LaraguardController;

class SignOutController extends BaseController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->get('laraguard_panel')->logout();
    
        return to_route($request->get('laraguard_panel')->getRouteName('login'))->with('message', __('laraguard::login.logout_successfull'));
    }
}
