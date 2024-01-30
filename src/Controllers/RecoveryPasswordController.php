<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use S4mpp\Laraguard\Notifications\ResetPassword;
use S4mpp\Laraguard\Controllers\LaraguardController;
use S4mpp\Laraguard\Requests\RecoveryPasswordSolicitationRequest;
use S4mpp\Laraguard\Notifications\ResetPassword as NotificationsResetPassword;

class RecoveryPasswordController extends Controller
{
    public function index()
    {
        $panel = Laraguard::currentPanel();

        $page_title = 'Alterar senha';

        $panel_title = $panel->getTitle();

        return view('laraguard::auth.password-recovery', compact('panel', 'page_title', 'panel_title'));
    }

    public function sendLink(RecoveryPasswordSolicitationRequest $request)
    {
        $panel = Laraguard::currentPanel();

        $user = Auth::guard($panel->getGuardName())->getProvider()->retrieveByCredentials(['email' => $request->email]);
        
        if(!$user)
        {
            return redirect()->back()->withErrors('Email/account not found.')->withInput();
        }

        $status = $panel->sendLinkRecoveryPassword($user);

        return $status === PasswordBroker::RESET_LINK_SENT
            ? redirect()->back()->withMessage(__($status))
            : back()->withErrors(['email' => [__($status)]])->withInput();
    }
}
