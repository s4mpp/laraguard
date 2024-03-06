<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Concerns\Auth;
use Illuminate\Http\{RedirectResponse, Request};

/**
 * @codeCoverageIgnore
 */
final class SignOutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');
        
        Auth::logout($panel->getGuardName());

        return to_route($panel->getRouteName('login'))
            ->with('message', __('laraguard::my_account.logout_successfull'));
    }
}
