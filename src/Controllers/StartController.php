<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\{RedirectResponse, Request};

/**
 * @codeCoverageIgnore
 */
final class StartController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        if (! Auth::guard($panel->getGuardName())->check()) {
            return to_route($panel->getRouteName('login'));
        }

        return to_route($panel->getRouteName($panel->getStartModule()->getSlug(), 'index'));
    }
}
