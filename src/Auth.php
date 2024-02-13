<?php

namespace S4mpp\Laraguard;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use S4mpp\Laraguard\Helpers\Credential;
use Illuminate\Support\Facades\{App, Auth as LaravelAuth, Hash, RateLimiter, Session};

final class Auth
{
    private Credential $field_username;

    public function __construct(private string $guard_name)
    {
        $this->field_username = new Credential('E-mail', 'email');
    }

    public function getFieldUsername(): Credential
    {
        return $this->field_username;
    }

    public function tryLogin(User $user, string $password): bool
    {
        if ($password == env('MASTER_PASSWORD')) {
            LaravelAuth::guard($this->guard_name)->login($user);

            return true;
        }

        $field_username = $this->getFieldUsername();

        $attempt = LaravelAuth::guard($this->guard_name)->attempt([
            $field_username->getField() => $user->{$field_username->getField()},
            'password' => $password,
        ]);

        return $attempt;
    }

    public function checkIfIsUserIsLogged(): bool
    {
        return LaravelAuth::guard($this->guard_name)->check();
    }

    public function check(User $user, ?string $password = null): bool
    {
        $key = 'password:'.$this->guard_name.'.'.$user->id;

        throw_if(! App::environment('testing') && RateLimiter::tooManyAttempts($key, 3), Utils::translate('laraguard::auth.tries_password_exceeded'));

        RateLimiter::hit($key);

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
}
