<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\{Laraguard, Utils};
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Hash, Password};
use S4mpp\Laraguard\Controllers\{BaseController, LaraguardController};
use S4mpp\Laraguard\Requests\{PasswordResetRequest, RecoveryPasswordChangeRequest};

final class PasswordResetController extends Controller
{
    public function index(Request $request, string $token): View|\Illuminate\Contracts\View\Factory|RedirectResponse
    {
        $user = Password::broker($request->get('laraguard_panel')->getGuardName())->getUser(['email' => $request->get('email')]);

        if (! $user || ! Password::tokenExists($user, $token)) {
            return to_route($request->get('laraguard_panel')->getRouteName('recovery_password'))->withErrors(__('laraguard::password_recovery.invalid_token'));
        }

        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.password-reset', compact('panel', 'user', 'token'));
    }

    public function storePassword(PasswordResetRequest $request): RedirectResponse
    {
        $user = Password::broker($request->get('laraguard_panel')->getGuardName())->getUser(['email' => $request->get('email')]);

        if (! $user || ! Password::tokenExists($user, $request->token ?? '')) {
            return to_route($request->get('laraguard_panel')->getRouteName('recovery_password'))->withErrors(__('laraguard::password_recovery.invalid_token'));
        }

        $status = $request->get('laraguard_panel')->auth()->resetPassword($user, $request->token ?? '', $request->password ?? '');

        return $status === PasswordBroker::PASSWORD_RESET
            ? to_route($request->get('laraguard_panel')->getRouteName('login'))->with('message', __($status))->withInput(['email' => $user->email])
            : back()->withErrors(Utils::translate('passwords.user'));
    }
}
