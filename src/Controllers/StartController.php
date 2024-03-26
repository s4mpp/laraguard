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

        $start_module = $panel->getStartModule();

        return ($start_module)
            ? to_route($panel->getRouteName($start_module->getSlug(), 'index'))
            : to_route($panel->getRouteName('login'))->with('message', 'Nenhum módulo definido neste painel');

    }
}
