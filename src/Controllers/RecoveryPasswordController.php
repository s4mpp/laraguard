<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Password};
use S4mpp\Laraguard\Requests\RecoveryPasswordSolicitationRequest;
use S4mpp\Laraguard\Controllers\{BaseController, LaraguardController};
use S4mpp\Laraguard\Notifications\{ResetPassword, ResetPassword as NotificationsResetPassword};

final class RecoveryPasswordController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        $panel_title = $request->get('laraguard_panel')->getTitle();

        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.password-recovery', compact('panel'));
    }

    public function sendLink(RecoveryPasswordSolicitationRequest $request): RedirectResponse
    {
        $user = Auth::guard($request->get('laraguard_panel')->getGuardName())->getProvider()->retrieveByCredentials(['email' => $request->email ?? null]);

        if (! $user) {
            return back()->withErrors(__('laraguard::recovery_password.account_not_found'))->withInput();
        }

        $status = $request->get('laraguard_panel')->sendLinkRecoveryPassword($user);

        if (! is_string($status)) {
            return back()->with('message', __('laraguard::recovery_password.fail_to_send_email'));
        }

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('message', __($status))
            : back()->withErrors(['email' => [__($status)]])->withInput();
    }
}
