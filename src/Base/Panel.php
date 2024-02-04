<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Utils;
use S4mpp\Laraguard\Base\Module;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Navigation\Page;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use S4mpp\Laraguard\Navigation\MenuItem;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Auth\Passwords\CanResetPassword;
use S4mpp\Laraguard\Notifications\ResetPassword;
use S4mpp\Laraguard\Controllers\PersonalDataController;

final class Panel
{
	private $field_username = ['field' => 'email', 'title' => 'E-mail'];

	private bool $allow_auto_register = false;

	private array $modules = [];

	public function __construct(private string $title, private string $prefix = '', private string $guard_name = 'web')
	{
		$my_account = $this->addModule('My account', 'my-account')->controller(PersonalDataController::class)->addIndex()->hideInMenu();

		$my_account->addPage('Save personal data', 'save-personal-data')->method('put')->action('savePersonalData');
		$my_account->addPage('Save new password', 'change-password')->method('put')->action('changePassword');
	}

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

	public function getRouteName(string ...$path): string
	{
		return 'lg.'.$this->getGuardName().'.'.join('.', $path);
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

	public function checkPassword(User $user, string $password): void
	{
		throw_if(RateLimiter::tooManyAttempts('password:'.$user->id, 3), 'Você excedeu a quantidade de tentativas por tempo. Aguarde alguns segundos e tente novamente.');

		RateLimiter::hit('password:'.$user->id);
        
		throw_if(!Hash::check($password, $user->password), 'Senha inválida. Tente novamente');
	}
	
	public function logout(): bool
	{
		Auth::guard($this->getGuardName())->logout();

		Session::invalidate();

		Session::regenerateToken();
 		
		return !$this->checkIfIsUserIsLogged();
	}

	public function addModule(string $title, string $slug = null)
	{
		$module = new Module($title, $slug);
		
		$this->modules[$module->getSlug()] = $module;

		return $module;
	}

	public function getModules(): array
	{
		return $this->modules;
	}

	public static function current()
	{
		return Utils::getSegmentRouteName(1, request()->route()->getAction('as'));
	}

	/**
	 * 	@deprecated
	 */
	public function currentModule()
	{
		$route_segment = Utils::getSegmentRouteName(2,  request()->route()->getAction('as'));

		return $this->getModule($route_segment);
	}

	public function getModule(string $module_name = null): ?Module
	{
		return $this->modules[$module_name] ?? null;
	}


	public function getLayout(string $view = null, array $data = [])
	{	
		$module = $this->currentModule();

		return $module->currentPage()->render($view, array_merge($data, [
			'panel' => $this,
			'guard_name' => $this->getGuardName(),
			'menu' => $this->getMenu(),
			'my_account_url' => route($this->getRouteName('my-account', 'index')),
			'logout_url' => route($this->getRouteName('signout')),
			'module_title' => $module->getTitle(),
		]));
	}

	public function getMenu(): array
	{
		$current_route = request()?->route()?->getAction('as');

		foreach($this->getModules() as $module)
		{
			if(!$module->canShowInMenu())
			{
				continue;
			}

			$menu_item = (new MenuItem($module->getTitle(), $module->getSlug()));

			$module_route = $this->getRouteName($module->getSlug(), 'index');
			
			$module_route_prefix = $this->getRouteName($module->getSlug());
			
			$menu_item->setAction(route($module_route));

			if(strpos($current_route, $module_route_prefix) !== false)
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