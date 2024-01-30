<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Panel;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Tests\TestCase;

class GuardTest extends TestCase
{
	private $panel;

	protected function setUp(): void
	{
		parent::setUp();

		$this->panel = new Panel('Panel title', 'panel-prefix', 'administrator');
	}

	public function test_create_instance()
	{
		$this->assertCount(0, $this->panel->getPages());

		$this->assertSame('Panel title', $this->panel->getTitle());
		$this->assertSame('administrator', $this->panel->getGuardName());
		$this->assertSame('panel-prefix', $this->panel->getPrefix());

		$this->assertFalse($this->panel->hasAutoRegister());
	}

	public function test_get_name_fields()
	{
		$this->assertSame(['field' => 'email', 'title' => 'E-mail'], $this->panel->getFieldUsername());
		
		$this->assertSame('email', $this->panel->getFieldUsername('field'));
		
		$this->assertSame('E-mail', $this->panel->getFieldUsername('title'));
	}

	public function test_get_route_name()
	{
		$this->assertSame('lg.administrator.login', $this->panel->getRouteName('login'));
	}

	public function test_set_auto_register()
	{
		$current_panel = $this->panel->allowAutoRegister();

		$this->assertInstanceOf(Panel::class, $current_panel);

		$this->assertTrue($this->panel->hasAutoRegister());
	}
}