<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use S4mpp\Laraguard\Controllers\LaraguardController;
use S4mpp\Laraguard\Requests\RecoveryPasswordSolicitationRequest;

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

        $url = ResetPassword::createUrlUsing(function ($user, string $token) use ($panel)
        {
            return route($panel->getRouteName('change_password'), ['token' => $token, 'email' => $user->email]);
        });

        $status = Password::broker($panel->getGuardName())->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? redirect()->back()->withMessage(__($status))
            : back()->withErrors(['email' => [__($status)]])->withInput();
    }
}
