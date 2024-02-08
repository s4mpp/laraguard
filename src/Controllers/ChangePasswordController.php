<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Utils;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword;
use S4mpp\Laraguard\Controllers\BaseController;
use S4mpp\Laraguard\Controllers\LaraguardController;
use S4mpp\Laraguard\Requests\RecoveryPasswordChangeRequest;

class ChangePasswordController extends BaseController
{
    public function index(Request $request, string $token): \Illuminate\Contracts\View\View | \Illuminate\Contracts\View\Factory | RedirectResponse
    {
        $user = Password::broker($request->get('laraguard_panel')->getGuardName())->getUser(['email' => $request->get('email')]);

        if(!$user || !Password::tokenExists($user, $token))
        {
            return to_route($request->get('laraguard_panel')->getRouteName('recovery_password'))->withErrors('Invalid token');
        }

        $page_title = 'Alterar senha';

        $panel_title = $request->get('laraguard_panel')->getTitle();

        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.change-password', compact('panel', 'user', 'token', 'panel_title', 'page_title'));
    }

    public function storePassword(RecoveryPasswordChangeRequest $request): RedirectResponse
    {
        $user = Password::broker($request->get('laraguard_panel')->getGuardName())->getUser(['email' => $request->get('email')]);
        
        if(!$user || !Password::tokenExists($user, $request->token ?? ''))
        {
            return to_route($request->get('laraguard_panel')->getRouteName('recovery_password'))->withErrors('Invalid token');
        }

        $status = $request->get('laraguard_panel')->resetPassword($user, $request->token ?? '', $request->password ?? '');
        
        return $status === PasswordBroker::PASSWORD_RESET
            ? to_route($request->get('laraguard_panel')->getRouteName('login'))->with('message', __($status))->withInput(['email' => $user->email])
            : back()->withErrors(Utils::translate('passwords.user'));
    }
}
