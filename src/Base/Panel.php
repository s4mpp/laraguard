<?php

namespace S4mpp\Laraguard\Base;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use S4mpp\Laraguard\Navigation\{MenuItem, Page};
use S4mpp\Laraguard\Navigation\{Menu, MenuSection};
use S4mpp\Laraguard\Controllers\PersonalDataController;
use Illuminate\Auth\Passwords\{CanResetPassword, PasswordBroker};
use Illuminate\Support\Facades\{App, Auth, Hash, Password, RateLimiter, Session};
use S4mpp\Laraguard\{Auth as LaraguardAuth, Laraguard, Password as LaraguardPassword, Utils};

final class Panel
{
    private bool $allow_auto_register = false;

    /**
     * @var array<Module>
     */
    private array $modules = [];

    private LaraguardAuth $auth;

    private LaraguardPassword $password;

    private Menu $menu;

    /**
     * @var array<MenuSection>
     */
    private array $menu_sections = [];

    public function __construct(private string $title, private string $prefix = '', private string $guard_name = 'web')
    {
        $my_account = $this->addModule('laraguard::my_account.title', 'my-account')
            ->translateTitle()
            ->controller(PersonalDataController::class)
            ->addIndex()
            ->hideInMenu();

        $my_account->addPage('', 'save-personal-data', 'save-personal-data')->method('put')->action('savePersonalData');
        $my_account->addPage('', 'change-password', 'change-password')->method('put')->action('changePassword');

        $this->auth = new LaraguardAuth($guard_name);

        $this->password = new LaraguardPassword($guard_name);

        $this->menu = new Menu(fn (...$params) => $this->getRouteName(...$params));
    }

    public function auth(): LaraguardAuth
    {
        return $this->auth;
    }

    public function password(): LaraguardPassword
    {
        return $this->password;
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

    public function getModel(): ?Model
    {
        $model_name = Auth::guard($this->getGuardName())->getProvider()->getModel();

        $model = new $model_name();

        if (is_subclass_of($model, Model::class)) {
            return $model;
        }

        return null;
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

    /**
     * @return array<MenuSection>
     */
    public function getMenuSections(): array
    {
        return $this->menu_sections;
    }

    public function getMenuSection(string $slug): ?MenuSection
    {
        return $this->menu_sections[$slug] ?? null;
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
        $this->menu->generate($this->getModules());

        $this->menu->activate(request()?->route()?->getAction('as'));

        return $this->getModule(Module::current())?->getLayout($view, array_merge($data, [
            'panel' => $this,
            'guard_name' => $this->getGuardName(),
            'menu' => $this->menu->getLinks(),
            'my_account_url' => route($this->getRouteName('my-account', 'index')),
            'logout_url' => route($this->getRouteName('signout')),
        ]));
    }

    public function addSection(string $title, string $slug, array $modules): self
    {
        $section = new MenuSection($title, $slug);

        $this->menu_sections[$slug] = $section;

        foreach ($modules as $module) {
            $module->onSection($section);
        }

        return $this;
    }
}
