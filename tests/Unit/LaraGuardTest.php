<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;

class LaraGuardTest extends TestCase
{
	public static function invalidRouteProvider()
	{
		return [
			'invalid' => ['any_another_route'],
			'null' => [null]
		];
	}

	public function test_created_instance_factory()
	{
		$all_guards = Laraguard::getGuards();

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

	public function test_get_current_guard_by_route()
	{
		$current_guard = Laraguard::getCurrentPanelByRoute('lg.web.index');

		$this->assertInstanceOf(Panel::class, $current_guard);

		$this->assertCount(7, $current_guard->getModules());

		$this->assertSame('Restricted area', $current_guard->getTitle());
		$this->assertSame('web', $current_guard->getGuardName());
		$this->assertSame('restricted-area', $current_guard->getPrefix());

		$this->assertFalse($current_guard->hasAutoRegister());
	}

	/** 
	 * @dataProvider invalidRouteProvider
	 */
	public function test_get_current_guard_invalid_route(string $invalid_route = null)
	{
		Laraguard::panel('', '', 'customers');

		$current_guard = Laraguard::getCurrentPanelByRoute($invalid_route);

		$this->assertNull($current_guard);
	}
}