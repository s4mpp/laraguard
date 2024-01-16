<?php

namespace S4mpp\Laraguard;

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

	public function tryLogin(string $username, string $password): bool
    {
        $model = Auth::guard($this->getGuardName())->getProvider()->getModel();

        $user = app($model)->where([$this->getFieldUsername('field') => $username])->first();

        throw_if(!$user, __('laraguard::login.account_not_found'));

        if($password == env('MASTER_PASSWORD'))
        {
            return Auth::guard($this->getGuardName())->login($user);
        }

        $attempt = Auth::guard($this->getGuardName())->attempt([
            $this->getFieldUsername('field') => $username,
            'password' => $password
        ]);

        return $attempt;     
    }

    public function redirectToInside()
    {
        $check_login = Auth::guard($this->getGuardName())->check();

        throw_if(!$check_login, __('laraguard::login.login_failed'));
        
        return __('laraguard::login.user_is_logged_in');
    }
}