<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use S4mpp\Laraguard\Controllers\BaseController;
use S4mpp\Laraguard\Notifications\ResetPassword;
use S4mpp\Laraguard\Controllers\LaraguardController;
use S4mpp\Laraguard\Requests\RecoveryPasswordSolicitationRequest;
use S4mpp\Laraguard\Notifications\ResetPassword as NotificationsResetPassword;

class RecoveryPasswordController extends BaseController
{
    public function index(Request $request): \Illuminate\Contracts\View\View | \Illuminate\Contracts\View\Factory
    {
        $page_title = 'Alterar senha';

        $panel_title = $request->get('laraguard_panel')->getTitle();

        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.password-recovery', compact('panel', 'page_title', 'panel_title'));
    }

    public function sendLink(RecoveryPasswordSolicitationRequest $request): RedirectResponse
    {
        $user = Auth::guard($request->get('laraguard_panel')->getGuardName())->getProvider()->retrieveByCredentials(['email' => $request->email ?? null]);
        
        if(!$user)
        {
            return back()->withErrors('Email/account not found.')->withInput();
        }

        $status = $request->get('laraguard_panel')->sendLinkRecoveryPassword($user);

        if(!is_string($status))
        {
            return back()->with('message', 'Fail on execute this action.');
        }
        
        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('message', __($status))
            : back()->withErrors(['email' => [__($status)]])->withInput();
    }
}
