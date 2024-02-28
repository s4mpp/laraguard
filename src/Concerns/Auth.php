<?php

namespace S4mpp\Laraguard\Concerns;

use Illuminate\Foundation\Auth\User;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Traits\HasUser;
use S4mpp\Laraguard\Helpers\Credential;
use S4mpp\Laraguard\Traits\WithRateLimiter;
use Illuminate\Support\Facades\{App, Auth as LaravelAuth, Hash, Password, RateLimiter, Session};

abstract class Auth
{
    public static function tryLogin(Panel $panel, User $user, string $password): bool
    {
        if ($password == env('MASTER_PASSWORD')) {
            LaravelAuth::guard($panel->getGuardName())->login($user);

            return true;
        }

        $field = $panel->getCredential()->getField();

        $attempt = LaravelAuth::guard($panel->getGuardName())->attempt([
            $field => $user->{$field},
            'password' => $password,
        ]);

        return $attempt;
    }

    public static function logout(Panel $panel): bool
    {
        LaravelAuth::guard($panel->getGuardName())->logout();

        Session::invalidate();

        Session::regenerateToken();

        return ! LaravelAuth::guard($panel->getGuardName())->check();
    }
}
