<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Helpers\Credential;
use S4mpp\Laraguard\Navigation\{MenuItem, MenuSection};
use Workbench\App\Models\User;

final class PanelTest extends TestCase
{
    public function test_create_instance(): void
    {
        $panel = new Panel('Panel title', 'panel-prefix', 'administrator');

        $credential = $panel->getCredential();

        $this->assertCount(0, $panel->getModules());
        $this->assertFalse($panel->hasAutoRegister());
        $this->assertSame('lg.administrator.login', $panel->getRouteName('login'));

        $this->assertSame('Panel title', $panel->getTitle());
        $this->assertSame('administrator', $panel->getGuardName());
        $this->assertSame('panel-prefix', $panel->getPrefix());

        $this->assertInstanceOf(Credential::class, $credential);
        $this->assertSame('E-mail', $credential->getTitle());
        $this->assertSame('email', $credential->getField());
    }


    public function test_get_menu_sections(): void
    {
        $panel = Laraguard::getPanel('web');

        $sections = $panel->getMenuSections();

        $this->assertIsArray($sections);
        $this->assertContainsOnlyInstancesOf(MenuSection::class, $sections);
    }

    public function test_get_menu_section(): void
    {
        $panel = Laraguard::getPanel('web');

        $section = $panel->getMenuSection('section');

        $this->assertInstanceOf(MenuSection::class, $section);
        $this->assertSame('Section', $section->getTitle());
        $this->assertSame('section', $section->getSlug());
    }

    public function test_get_menu(): void
    {
        $panel = Laraguard::getPanel('web');

        $menu = $panel->generateMenu()->getLinks();

        $this->assertIsArray($menu);

        $this->assertContainsOnlyInstancesOf(MenuItem::class, $menu);
    }

    public function test_get_start_module(): void
    {
        $panel = Laraguard::getPanel('web');

        $start_module = $panel->getStartModule();

        $this->assertSame('home', $start_module->getSlug());
    }

    public function test_set_auto_register(): void
    {
        $panel = new Panel('', '', '');

        $panel = $panel->allowAutoRegister();

        $this->assertTrue($panel->hasAutoRegister());
    }

    public function test_get_module(): void
    {
        $panel = new Panel('', '', '');

        $this->assertNull($panel->getModule(null));
    }

    public function test_get_subdomain(): void
    {
        $panel = new Panel('', '', '');

        $panel->subdomain('subdomain.example.com');

        $this->assertSame('subdomain.example.com', $panel->getSubdomain());
    }


    public function test_get_model()
    {
        $panel = new Panel('', '', 'web');

        $model = $panel->getModel();

        $this->assertInstanceOf(User::class, $model);
    }
}
