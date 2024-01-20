<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Tests\TestCase;

class GuardTest extends TestCase
{
	public function test_create_instance_factory()
	{
		Laraguard::guard('Customer panel', 'customer', 'customer');

		$all_guards = Laraguard::getGuards();

		$guard_customer = Laraguard::getGuard('customer');
		
		$this->assertIsArray($all_guards);

		$this->assertInstanceOf(Guard::class, $guard_customer);

		$this->assertSame('Customer panel', $guard_customer->getTitle());
		$this->assertSame('customer', $guard_customer->getGuardName());
		$this->assertSame('customer', $guard_customer->getPrefix());
	}

	public function test_get_current_guard()
	{
		Laraguard::guard('Panel title', 'panel-prefix', 'administrators');

		$current_guard = Laraguard::getCurrentGuard('lg.administrators.index');

		$this->assertInstanceOf(Guard::class, $current_guard);

		$this->assertSame('Panel title', $current_guard->getTitle());
		$this->assertSame('administrators', $current_guard->getGuardName());
		$this->assertSame('panel-prefix', $current_guard->getPrefix());
	}

	public function test_create_instance()
	{
		$new_guard = new Guard('Panel title', 'panel-prefix', 'administrators');

		$this->assertSame('Panel title', $new_guard->getTitle());
		$this->assertSame('administrators', $new_guard->getGuardName());
		$this->assertSame('panel-prefix', $new_guard->getPrefix());
	}

	public function test_get_name_fields()
	{
		$new_guard = new Guard('', '', '');

		$this->assertSame(['field' => 'email', 'title' => 'E-mail'], $new_guard->getFieldUsername());
		
		$this->assertSame('email', $new_guard->getFieldUsername('field'));
		
		$this->assertSame('E-mail', $new_guard->getFieldUsername('title'));
	}

	public function test_get_route_names()
	{
		$new_guard = new Guard('', '', 'web');

		$this->assertSame('lg.web.login', $new_guard->getRouteName('login'));
	}

	public function test_get_current_guard_invalid_route()
	{
		Laraguard::guard('', '', 'customers');

		$current_guard = Laraguard::getCurrentGuard('any_another_route');

		$this->assertNull($current_guard);
	}
}