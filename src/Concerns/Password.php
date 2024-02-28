<?php

namespace S4mpp\Laraguard\Concerns;

use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Traits\HasUser;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Traits\WithRateLimiter;
use Illuminate\Auth\Passwords\PasswordBroker;
use S4mpp\Laraguard\Notifications\ResetPassword;
use Illuminate\Support\Facades\Password as PasswordFacade;
use S4mpp\Laraguard\Helpers\Utils;

abstract class Password
{
    public static function sendLinkReset(Panel $panel, User $user): mixed
    {
        return PasswordFacade::broker($panel->getGuardName())->sendResetLink(['email' => $user->email], function ($user, $token) use ($panel) {
            $url = route($panel->getRouteName('change_password'), ['token' => $token, 'email' => $user->email]);

            $user->notify(new ResetPassword($url));

            return PasswordBroker::RESET_LINK_SENT;
        });
    }

    public static function reset(Panel $panel, User $user, string $token, string $new_password): mixed
    {
        return PasswordFacade::broker($panel->getGuardName())->reset([
            'email' => $user->email,
            'token' => $token,
            'password' => $new_password,
        ], function (User $user, string $password) {

            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        });
    }
}
