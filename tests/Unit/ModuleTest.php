<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Base\Module;
use S4mpp\Laraguard\Base\Page;
use S4mpp\Laraguard\Tests\TestCase;

class ModuleTest extends TestCase
{
	public function test_create_instance()
	{
		$module = new Module('Module title', 'module-prefix');

		$this->assertEquals('Module title', $module->getTitle());
		$this->assertEquals('module-prefix', $module->getSlug());
	}

	public function test_get_page_on_creating_module()
	{
		$module = new Module('', '');

		$this->assertNull($module->getPage(null));
	}

	public function test_get_page_index()
	{
		$module = (new Module('', ''))->addIndex();

		$index = $module->getPage('index');

		$this->assertCount(1, $module->getPages());
	}

	public function test_hide_on_menu()
	{
		$module = new Module('', '');

		$module->hideInMenu();

		$this->assertFalse($module->canShowInMenu());
	}
}