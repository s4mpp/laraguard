<?php

namespace S4mpp\Laraguard\Controllers;

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
        $panel = $request->get('laraguard_panel');
        
        Auth::logout($panel);

        return to_route($request->get('laraguard_panel')
            ->getRouteName('login'))
            ->with('message', __('laraguard::my_account.logout_successfull'));
    }
}
