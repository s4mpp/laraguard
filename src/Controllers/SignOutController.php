<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{RedirectResponse, Request};
use S4mpp\Laraguard\Controllers\{BaseController, LaraguardController};

final class SignOutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->get('laraguard_panel')->logout();

        return to_route($request->get('laraguard_panel')
            ->getRouteName('login'))
            ->with('message', __('laraguard::auth.logout_successfull'));
    }
}
