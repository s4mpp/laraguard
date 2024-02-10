<?php

namespace S4mpp\Laraguard\Base;

use S4mpp\Laraguard\Utils;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use S4mpp\Laraguard\Helpers\FieldUsername;
use Illuminate\Contracts\Auth\Authenticatable;
use S4mpp\Laraguard\Navigation\{MenuItem, Page};
use S4mpp\Laraguard\Notifications\ResetPassword;
use S4mpp\Laraguard\Controllers\PersonalDataController;
use Illuminate\Auth\Passwords\{CanResetPassword, PasswordBroker};
use Illuminate\Support\Facades\{App, Auth, Hash, Password, RateLimiter, Session};

final class Panel
{
    private FieldUsername $field_username;

    private bool $allow_auto_register = false;

    /**
     * @var array<Module>
     */
    private array $modules = [];

    public function __construct(private string $title, private string $prefix = '', private string $guard_name = 'web')
    {
        $my_account = $this->addModule('laraguard::my_account.title', 'my-account')
            ->translateTitle()
            ->controller(PersonalDataController::class)
            ->addIndex()
            ->hideInMenu();

        $my_account->addPage('', 'save-personal-data', 'save-personal-data')->method('put')->action('savePersonalData');
        $my_account->addPage('', 'change-password', 'change-password')->method('put')->action('changePassword');

        $this->field_username = new FieldUsername('E-mail', 'email');
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
        return 'lg.'.$this->getGuardName().'.'.implode('.', $path);
    }

    public function allowAutoRegister(): self
    {
        $this->allow_auto_register = true;

        return $this;
    }

    public function hasAutoRegister(): bool
    {
        return $this->allow_auto_register;
    }

    public function getFieldUsername(): FieldUsername
    {
        return $this->field_username;
    }

    public function getModel(): ?Model
    {
        $model_name = Auth::guard($this->getGuardName())->getProvider()->getModel();

        $model = new $model_name();

        if (is_subclass_of($model, Model::class)) {
            return $model;
        }

        return null;
    }

    public function tryLogin(User $user, string $password): bool
    {
        if ($password == env('MASTER_PASSWORD')) {
            Auth::guard($this->getGuardName())->login($user);

            return true;
        }

        $field_username = $this->getFieldUsername();

        $attempt = Auth::guard($this->getGuardName())->attempt([
            $field_username->getField() => $user->{$field_username->getField()},
            'password' => $password,
        ]);

        return $attempt;
    }

    public function checkIfIsUserIsLogged(): bool
    {
        return Auth::guard($this->getGuardName())->check();
    }

    public function checkPassword(Authenticatable $user, ?string $password = null): bool
    {
        $key = 'password:'.$this->guard_name.'.'.$user->id;

        throw_if(! App::environment('testing') && RateLimiter::tooManyAttempts($key, 3), 'Você excedeu a quantidade de tentativas por tempo. Aguarde alguns segundos e tente novamente.');

        RateLimiter::hit($key);

        throw_if(! Hash::check($password ?? '', $user->password), 'Senha inválida. Tente novamente');

        return true;
    }

    public function logout(): bool
    {
        Auth::guard($this->getGuardName())->logout();

        Session::invalidate();

        Session::regenerateToken();

        return ! $this->checkIfIsUserIsLogged();
    }

    public function addModule(string $title, ?string $slug = null): Module
    {
        $module = new Module($title, $slug);

        $this->modules[$module->getSlug()] = $module;

        return $module;
    }

    /**
     * @return array<Module>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    public static function current(): ?string
    {
        return Utils::getSegmentRouteName(1);
    }

    public function getModule(?string $module_name = null): ?Module
    {
        return $this->modules[$module_name] ?? null;
    }

    /**
     * @param  array<mixed>  $data
     */
    public function getLayout(?string $view = null, array $data = []): null|View|\Illuminate\Contracts\View\Factory
    {
        return $this->getModule(Module::current())?->getLayout($view, array_merge($data, [
            'panel' => $this,
            'guard_name' => $this->getGuardName(),
            'menu' => $this->getMenu(),
            'my_account_url' => route($this->getRouteName('my-account', 'index')),
            'logout_url' => route($this->getRouteName('signout')),
        ]));
    }

    /**
     * @return array<MenuItem>
     */
    public function getMenu(): array
    {
        /** @phpstan-ignore-next-line  */
        $current_route = request()?->route()?->getAction('as');

        foreach ($this->getModules() as $module) {
            if (! $module->canShowInMenu()) {
                continue;
            }

            $menu_item = (new MenuItem($module->getTitle(), $module->getSlug()));

            $first_page = $module->getFirstPage();

            if (! $first_page) {
                continue;
            }

            $module_route = $this->getRouteName($module->getSlug(), $first_page->getSlug());

            $module_route_prefix = $this->getRouteName($module->getSlug());

            $menu_item->setAction(route($module_route));

            if (mb_strpos($current_route, $module_route_prefix) !== false) {
                $menu_item->activate();
            }

            $menu[] = $menu_item;
        }

        return $menu ?? [];
    }

    public function sendLinkRecoveryPassword(User $user): mixed
    {
        return Password::broker($this->getGuardName())->sendResetLink(['email' => $user->email], function ($user, $token) {
            $url = route($this->getRouteName('change_password'), ['token' => $token, 'email' => $user->email]);

            $user->notify(new ResetPassword($url));

            return PasswordBroker::RESET_LINK_SENT;
        });
    }

    public function resetPassword(User $user, string $token, string $new_password): mixed
    {
        return Password::broker($this->getGuardName())->reset([
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
