<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{RedirectResponse, Request};

final class StartController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $panel = $request->get('laraguard_panel');

        if (! $panel->auth()->checkIfIsUserIsLogged()) {
            return to_route($panel->getRouteName('login'));
        }

        return to_route($panel->getRouteName('my-account', 'index'));
    }
}
