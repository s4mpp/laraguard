<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Tests\TestCase;

class GuardTest extends TestCase
{
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
}