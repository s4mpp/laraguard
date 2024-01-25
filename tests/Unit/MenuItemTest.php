<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Illuminate\Foundation\Auth\User;
use RuntimeException;
use S4mpp\Laraguard\Guard;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Navigation\MenuItem;
use Workbench\Database\Factories\CustomerFactory;
use Workbench\Database\Factories\UserFactory;

class MenuItemTest extends TestCase
{	
	public function test_creation_menu()
	{
		$menu = new MenuItem('Title menu', 'title-menu');

		$this->assertEquals('Title menu', $menu->getTitle());
		
		$this->assertEquals('title-menu', $menu->getSlug());
		
		$this->assertEquals('#', $menu->getAction());

		$this->assertFalse($menu->isActive());
	}

	public function test_activation_menu()
	{
		$menu = new MenuItem('', '');

		$menu->activate();

		$this->assertTrue($menu->isActive());
	}

	public function test_action_menu()
	{
		$menu = new MenuItem('', '');

		$menu->setAction('test');

		$this->assertEquals('test', $menu->getAction());
	}
}