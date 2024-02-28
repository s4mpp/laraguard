<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Utils;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\{Validator};
use S4mpp\Laraguard\Requests\SignInRequest;
use Illuminate\Http\{RedirectResponse, Request};
use S4mpp\Laraguard\Concerns\Auth as LaraguardAuth;

/**
 * @codeCoverageIgnore
 */
final class SignInController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.login', ['panel' => $panel]);
    }

    public function attempt(SignInRequest $request): RedirectResponse
    {
        
        $panel = $request->get('laraguard_panel');
        
        $field_username = $panel->getCredential();
        
        $field = $field_username->getField();
        
        try {
            Utils::rateLimiter();
            
            $model = $panel->getModel();

            $user = $model?->where([$field => $request->get('username')])->first();

            throw_if(! $user, __('laraguard::auth.account_not_found'));

            throw_if(! LaraguardAuth::tryLogin($panel, $user, $request->get('password')), __('laraguard::auth.invalid_credentials'));

            throw_if(! Auth::guard($panel->getGuardName())->check(), __('laraguard::auth.login_failed'));

            return to_route($panel->getRouteName($panel->getStartModule()->getSlug(), 'index'));
        } catch (\Exception $e) {
            return to_route($panel->getRouteName('login'))
                ->withErrors($e->getMessage())
                ->withInput(['username' => $request->get('username')]);
        }
    }
}
