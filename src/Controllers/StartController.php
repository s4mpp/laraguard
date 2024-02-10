<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{RedirectResponse, Request};
use S4mpp\Laraguard\Controllers\{BaseController, LaraguardController};

final class StartController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $panel = $request->get('laraguard_panel');

        if (! $panel->checkIfIsUserIsLogged()) {
            return to_route($panel->getRouteName('login'));
        }

        return to_route($panel->getRouteName('my-account', 'index'));
    }
}
