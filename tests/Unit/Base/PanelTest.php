<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Navigation\MenuItem;
use S4mpp\Laraguard\Helpers\Credential;

final class PanelTest extends TestCase
{
    private $panel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->panel = new Panel('Panel title', 'panel-prefix', 'administrator');
    }

    public function test_create_instance(): void
    {
        $this->assertCount(1, $this->panel->getModules());

        $this->assertSame('Panel title', $this->panel->getTitle());
        $this->assertSame('administrator', $this->panel->getGuardName());
        $this->assertSame('panel-prefix', $this->panel->getPrefix());

        $this->assertFalse($this->panel->hasAutoRegister());
    }

    public function test_get_name_fields(): void
    {
        $field_username = $this->panel->getFieldUsername();

        $this->assertInstanceOf(Credential::class, $field_username);

        $this->assertSame('E-mail', $field_username->getTitle());

        $this->assertSame('email', $field_username->getField());
    }

    public function test_get_route_name(): void
    {
        $this->assertSame('lg.administrator.login', $this->panel->getRouteName('login'));
    }

    public function test_set_auto_register(): void
    {
        $current_panel = $this->panel->allowAutoRegister();

        $this->assertInstanceOf(Panel::class, $current_panel);

        $this->assertTrue($this->panel->hasAutoRegister());
    }

    public function test_get_menu(): void
    {
        $panel = Laraguard::getPanel('web');

        $menu = $panel->getMenu();

        $this->assertIsArray($menu);

        $this->assertContainsOnlyInstancesOf(MenuItem::class, $menu);
    }

    public function test_get_module(): void
    {
        $panel = new Panel('', '', '');

        $this->assertNull($panel->getModule(null));
    }
}
