<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;

class LaraguardTest extends TestCase
{
	public function test_created_instance_factory()
	{
		$all_guards = Laraguard::getPanels();

		$this->assertIsArray($all_guards);
		$this->assertCount(2, $all_guards);
	}

	public function test_get_panel_by_guard()
	{
		$guard_customer = Laraguard::getPanel('customer'); 
		
		$this->assertInstanceOf(Panel::class, $guard_customer);

		$this->assertCount(1, $guard_customer->getModules());
		
		$this->assertSame('My account', $guard_customer->getTitle());
		$this->assertSame('customer', $guard_customer->getGuardName());
		$this->assertSame('customer-area', $guard_customer->getPrefix());
		
		$this->assertTrue($guard_customer->hasAutoRegister());
	}
}