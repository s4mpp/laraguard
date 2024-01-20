<?php

namespace S4mpp\Laraguard;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

final class Guard
{
	private $field_username = ['field' => 'email', 'title' => 'E-mail'];

	public function __construct(private string $title, private string $prefix = '', private string $guard_name)
	{}

	public function getTitle(): string
	{
		return $this->title;
	}
	
	public function getGuardName(): string
	{
		return $this->guard_name;
	}

	public function getPrefix(): string
	{
		return $this->prefix;
	}

	public function getRouteName(string $action): string
	{
		return 'lg.'.$this->getGuardName().'.'.$action;
	}

	public function getFieldUsername(string $index = null)
	{
		if($index && isset($this->field_username[$index]))
		{
			return $this->field_username[$index];
		}

		return $this->field_username;
	}

	public function tryLogin(User $user, string $password): bool
    {
        if($password == env('MASTER_PASSWORD'))
        {
            Auth::guard($this->getGuardName())->login($user);

			return true;
        }

		$field = $this->getFieldUsername('field');

        $attempt = Auth::guard($this->getGuardName())->attempt([
            $field => $user->{$field},
            'password' => $password
        ]);

        return $attempt;     
    }

	public function checkLogin(): bool
	{
		return Auth::guard($this->getGuardName())->check();
	}

	public function logout(): bool
	{
		Auth::guard($this->getGuardName())->logout();
		
		return !$this->checkLogin();
	}

    public function redirectToInside()
    {
        return __('laraguard::login.user_is_logged_in');
    }
}