<?php

namespace S4mpp\Laraguard;

use Closure;
use Illuminate\Foundation\Auth\User;
use S4mpp\Laraguard\Helpers\Credential;
use Illuminate\Contracts\Auth\PasswordBroker;
use S4mpp\Laraguard\Notifications\ResetPassword;
use Illuminate\Support\Facades\{App, Auth as LaravelAuth, Hash, Password, RateLimiter, Session};

final class Auth
{
    private Credential $credential_fields;

    public function __construct(private string $guard_name, private Closure $callback_get_route_name)
    {
        $this->credential_fields = new Credential('E-mail', 'email');
    }

    public function getCredentialFields(): Credential
    {
        return $this->credential_fields;
    }

    public function tryLogin(User $user, string $password): bool
    {
        if ($password == env('MASTER_PASSWORD')) {
            LaravelAuth::guard($this->guard_name)->login($user);

            return true;
        }

        $credential_fields = $this->getCredentialFields();

        $attempt = LaravelAuth::guard($this->guard_name)->attempt([
            $credential_fields->getField() => $user->{$credential_fields->getField()},
            'password' => $password,
        ]);

        return $attempt;
    }

    public function checkIfIsUserIsLogged(): bool
    {
        return LaravelAuth::guard($this->guard_name)->check();
    }

    public function checkPassword(User $user, ?string $password = null): bool
    {
        $id = (isset($user->id)) ? $user->id : rand();

        $key = 'password:'.$this->guard_name.'.'.$id;

        throw_if(! App::environment('testing') && RateLimiter::tooManyAttempts($key, 3), Utils::translate('laraguard::auth.tries_password_exceeded'));

        RateLimiter::hit($key);

        if (! isset($user->password)) {
            return false;
        }

        throw_if(! Hash::check($password ?? '', $user->password), Utils::translate('laraguard::auth.invalid_password'));

        return true;
    }

    public function logout(): bool
    {
        LaravelAuth::guard($this->guard_name)->logout();

        Session::invalidate();

        Session::regenerateToken();

        return ! $this->checkIfIsUserIsLogged();
    }

    public function sendLinkResetPassword(User $user): mixed
    {
        if (! isset($user->email) || ! isset($user->password)) {
            return false;
        }

        return Password::broker($this->guard_name)->sendResetLink(['email' => $user->email], function ($user, $token) {
            $url = route(call_user_func($this->callback_get_route_name, 'change_password'), ['token' => $token, 'email' => $user->email]);

            $user->notify(new ResetPassword($url));

            return PasswordBroker::RESET_LINK_SENT;
        });
    }

    public function resetPassword(User $user, string $token, string $new_password): mixed
    {
        if (! isset($user->email)) {
            return false;
        }

        return Password::broker($this->guard_name)->reset([
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
