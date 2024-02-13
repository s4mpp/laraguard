<?php

namespace S4mpp\Laraguard;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as FacadePassword;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Auth\Notifications\ResetPassword;

final class Password
{
    public function __construct(private string $guard_name)
    {
    }

    public function sendLink(User $user): mixed
    {
        return FacadePassword::broker($this->guard_name)->sendResetLink(['email' => $user->email], function ($user, $token) {
            $url = route($this->getRouteName('change_password'), ['token' => $token, 'email' => $user->email]);

            $user->notify(new ResetPassword($url));

            return PasswordBroker::RESET_LINK_SENT;
        });
    }

    public function reset(User $user, string $token, string $new_password): mixed
    {
        return FacadePassword::broker($this->guard_name)->reset([
            'email' => $user->email,
            'token' => $token,
            'password' => $new_password,
        ], function (User $user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        });
    }
}
