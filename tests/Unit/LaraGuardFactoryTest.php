<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Tests\TestCase;

class LaraGuardFactoryTest extends TestCase
{
	public static function invalidRouteProvider()
	{
		return [
			'invalid' => ['any_another_route'],
			'null' => [null]
		];
	}

	private $panel;

	public function test_created_instance_factory()
	{
		$all_guards = Laraguard::getGuards();

		$this->assertIsArray($all_guards);
		$this->assertCount(2, $all_guards);
	}

	public function test_get_panel_by_guard()
	{
		$guard_customer = Laraguard::getGuard('customer'); 
		
		$this->assertInstanceOf(Guard::class, $guard_customer);

		$this->assertCount(1, $guard_customer->getPages());

		$this->assertSame('My account', $guard_customer->getTitle());
		$this->assertSame('customer', $guard_customer->getGuardName());
		$this->assertSame('customer-area', $guard_customer->getPrefix());
	}

	public function test_get_current_guard_by_route()
	{
		$current_guard = Laraguard::getCurrentGuardByRoute('lg.customer.index');

		$this->assertInstanceOf(Guard::class, $current_guard);

		$this->assertCount(1, $current_guard->getPages());

		$this->assertSame('My account', $current_guard->getTitle());
		$this->assertSame('customer', $current_guard->getGuardName());
		$this->assertSame('customer-area', $current_guard->getPrefix());
	}

	/** 
	 * @dataProvider invalidRouteProvider
	 */
	public function test_get_current_guard_invalid_route(string $invalid_route = null)
	{
		Laraguard::guard('', '', 'customers');

		$current_guard = Laraguard::getCurrentGuardByRoute($invalid_route);

		$this->assertNull($current_guard);
	}
}