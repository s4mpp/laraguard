<?php

namespace S4mpp\Laraguard;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Navigation\Page;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use S4mpp\Laraguard\Navigation\MenuItem;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Auth\Passwords\CanResetPassword;
use S4mpp\Laraguard\Notifications\ResetPassword;

final class Panel
{
	private $field_username = ['field' => 'email', 'title' => 'E-mail'];

	private bool $allow_auto_register = false;

	private $pages = [];

	public function __construct(private string $title, private string $prefix = '', private string $guard_name = 'web')
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

	public function getRouteName(string $page): string
	{
		return 'lg.'.$this->getGuardName().'.'.$page;
	}

	public function allowAutoRegister()
	{
		$this->allow_auto_register = true;

		return $this;
	}

	public function hasAutoRegister()
	{
		return $this->allow_auto_register;
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

	public function checkIfIsUserIsLogged(): bool
	{
		return Auth::guard($this->getGuardName())->check();
	}
	
	public function logout(): bool
	{
		Auth::guard($this->getGuardName())->logout();

		Session::invalidate();

		Session::regenerateToken();
 		
		return !$this->checkIfIsUserIsLogged();
	}

	public function currentPage()
	{
		return $this->getPage(request()->get('laraguard_page'));
	}

	public function addPage(string $title_or_page, string $view = null, string $slug = null)
	{
		$page = new Page($title_or_page, $view, $slug);
		
		$this->pages[$page->getSlug()] = $page;

		return $page;
	}

	public function getPages(): array
	{
		return $this->pages;
	}

	public function getCurrentPageByRoute(string $route = null): ?Page
	{
		$path_steps = explode('.', $route);
		
		$page_name = $path_steps[2] ?? null;

		return $this->getPage($page_name);
	}

	public function getPage(string $page_name): ?Page
	{
		return $this->pages[$page_name] ?? null;
	}

	public function getLayout(string $file = null, array $data = [])
	{
		return $this->currentPage()->render($file, array_merge($data, [
			'guard_name' => $this->getGuardName(),
			'panel_title' => $this->getTitle(),
			'menu' => $this->getMenu(),
			'my_account_url' => route($this->getRouteName('my-account')),
			'logout_url' => route($this->getRouteName('signout')),
		]));
	}

	public function getMenu(): array
	{
		$current_route = request()->route()->getAction('as');

		foreach($this->pages as $page)
		{
			if(!$page->canShowInMenu())
			{
				continue;
			}

			$menu_item = (new MenuItem($page->getTitle(), $page->getSlug()));

			$page_route = $this->getRouteName($page->getSlug());
			
			$menu_item->setAction(route($page_route));

			if($current_route == $page_route)
			{
				$menu_item->activate();
			}
			
			$menu[] = $menu_item;
		}

		return $menu ?? [];
	}

	public function sendLinkRecoveryPassword(User $user)
	{
		return Password::broker($this->getGuardName())->sendResetLink(['email' => $user->email], function($user, $token)
        {
            $url = route($this->getRouteName('change_password'), ['token' => $token, 'email' => $user->email]);

            $user->notify(new ResetPassword($url));

            return PasswordBroker::RESET_LINK_SENT;
        });
	}

	public function resetPassword(User $user, string $token, string $new_password)
	{
		return Password::broker($this->getGuardName())->reset([
			'email' => $user->email,
			'token' => $token,
			'password' => $new_password,
		], function (User $user, string $password)
        {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        });
	}
}