<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Auth;

/**
 * @codeCoverageIgnore
 */
final class StartController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $panel = $request->get('laraguard_panel');

        if (! Auth::guard($panel->getGuardName())->check()) {
            return to_route($panel->getRouteName('login'));
        }

        return to_route($panel->getRouteName('my-account', 'index'));
    }
}
