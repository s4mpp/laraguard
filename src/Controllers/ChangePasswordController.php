<?php

namespace S4mpp\Laraguard\Controllers;


use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\CanResetPassword;
use S4mpp\Laraguard\Controllers\LaraguardController;
use S4mpp\Laraguard\Requests\RecoveryPasswordChangeRequest;

class ChangePasswordController extends Controller
{
    public function index(string $token)
    {
        $panel = Laraguard::currentPanel();
        
        $user = Password::broker($panel->getGuardName())->getUser(['email' => request()->get('email')]);

        if(!$user || !Password::tokenExists($user, $token))
        {
            return to_route($panel->getRouteName('recovery_password'))->withErrors('Invalid token');
        }

        $page_title = 'Alterar senha';

        $guard_title = $panel->getTitle();

        return view('laraguard::change-password', compact('guard', 'user', 'token', 'guard_title', 'page_title'));
    }

    public function storePassword(RecoveryPasswordChangeRequest $request)
    {
        $panel = Laraguard::currentPanel();
        
        $user = Password::broker($panel->getGuardName())->getUser(['email' => $request->get('email')]);
        
        if(!$user || !Password::tokenExists($user, $request->get('token')))
        {
            return to_route($panel->getRouteName('recovery_password'))->withErrors('Invalid token');
        }
        
        $status = Password::broker($panel->getGuardName())->reset($request->only('email', 'password', 'password_confirmation', 'token'), function (CanResetPassword $user, string $password)
        {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        });        
        
        return $status === Password::PASSWORD_RESET
            ? redirect()->route($panel->getRouteName('login'))->withMessage(__($status))->withInput(['email' => $user->email])
            : back()->withErrors(__('passwords.user'));
    }
}
