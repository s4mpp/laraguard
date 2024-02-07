<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword;
use S4mpp\Laraguard\Controllers\LaraguardController;
use S4mpp\Laraguard\Requests\RecoveryPasswordChangeRequest;

class ChangePasswordController extends Controller
{
    public function index(string $token): View | RedirectResponse
    {
        $panel = Laraguard::getPanel(Panel::current());
        
        $user = Password::broker($panel->getGuardName())->getUser(['email' => request()->get('email')]);

        if(!$user || !Password::tokenExists($user, $token))
        {
            return to_route($panel->getRouteName('recovery_password'))->withErrors('Invalid token');
        }

        $page_title = 'Alterar senha';

        $panel_title = $panel->getTitle();

        return view('laraguard::auth.change-password', compact('panel', 'user', 'token', 'panel_title', 'page_title'));
    }

    public function storePassword(RecoveryPasswordChangeRequest $request): RedirectResponse
    {
        $panel = Laraguard::getPanel(Panel::current());
        
        $user = Password::broker($panel->getGuardName())->getUser(['email' => $request->get('email')]);
        
        if(!$user || !Password::tokenExists($user, $request->get('token')))
        {
            return to_route($panel->getRouteName('recovery_password'))->withErrors('Invalid token');
        }

        $status = $panel->resetPassword($user, $request->get('token'), $request->get('password'));
        
        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route($panel->getRouteName('login'))->with('message', __($status))->withInput(['email' => $user->email])
            : back()->withErrors(__('passwords.user'));
    }
}
