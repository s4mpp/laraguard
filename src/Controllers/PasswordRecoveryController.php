<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Notifications\ResetPassword;
use S4mpp\Laraguard\Requests\RecoveryPasswordChangeRequest;
use S4mpp\Laraguard\Requests\RecoveryPasswordSolicitationRequest;

class PasswordRecoveryController extends Controller
{
    public function __construct()
    {
        $this->guard = Laraguard::getCurrentGuard();
    }

    public function index()
    {
        return view('laraguard::password-recovery', ['guard' => $this->guard]);
    }

    public function sendLink(RecoveryPasswordSolicitationRequest $request)
    {
        $user = Auth::guard($this->guard->getGuardName())->getProvider()->retrieveByCredentials(['email' => $request->email]);
        
        if(!$user)
        {
            return redirect()->back()->withErrors('Email/account not found.')->withInput();
        }

        ResetPassword::createUrlUsing(function ($user, string $token)
        {
            return route($this->guard->getRouteName('change_password'), ['token' => $token, 'email' => $user->email]);
        });

        $status = Password::broker($this->guard->getGuardName())->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? redirect()->back()->withMessage(__($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function changePassword(string $token)
    {
        $user = Password::broker($this->guard->getGuardName())->getUser(['email' => request()->get('email')]);

        if(!$user || !Password::tokenExists($user, $token))
        {
            return to_route($this->guard->getRouteName('recovery_password'))->withErrors('Invalid token');
        }

        return view('laraguard::change-password', compact('guard', 'user', 'token'));
    }

    public function storePassword(RecoveryPasswordChangeRequest $request)
    {        
        $user = Password::broker($this->guard->getGuardName())->getUser(['email' => $request->get('email')]);
        
        if(!Password::tokenExists($user, $request->get('token')))
        {
            return to_route($this->guard->getRouteName('change_password'), ['token' => $request->get('token')])->withErrors('Invalid token');
        }
        
        $status = Password::broker($this->guard->getGuardName())->reset($request->only('email', 'password', 'password_confirmation', 'token'), function (CanResetPassword $user, string $password)
        {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        });        
        
        return $status === Password::PASSWORD_RESET
            ? redirect()->route($this->guard->getRouteName('login'))->withMessage(__($status))->withInput(['email' => $user->email])
            : back()->withErrors(__('passwords.user'));
    }
}
