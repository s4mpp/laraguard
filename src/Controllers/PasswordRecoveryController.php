<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Utils;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Password};
use S4mpp\Laraguard\Controllers\{BaseController, LaraguardController};
use S4mpp\Laraguard\Notifications\{ResetPassword, ResetPassword as NotificationsResetPassword};
use S4mpp\Laraguard\Requests\{PasswordRecoverySolicitationRequest, RecoveryPasswordSolicitationRequest};

final class PasswordRecoveryController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        $panel_title = $request->get('laraguard_panel')->getTitle();

        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.password-recovery', compact('panel'));
    }

    public function sendLink(PasswordRecoverySolicitationRequest $request): RedirectResponse
    {
        try {
            $user = Auth::guard($request->get('laraguard_panel')->getGuardName())
                ->getProvider()
                ->retrieveByCredentials(['email' => $request->email ?? null]);

            throw_if(! $user, Utils::translate('laraguard::password_recovery.account_not_found'));

            $status = $request->get('laraguard_panel')->auth()->sendLinkResetPassword($user);

            throw_if(! is_string($status), Utils::translate('laraguard::password_recovery.fail_to_send_email'));

            return back()->with('message', Utils::translate($status));
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
}
