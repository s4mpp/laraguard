<?php

namespace S4mpp\Laraguard\Tests\Unit\Helpers;

use S4mpp\Laraguard\Helpers\Utils;
use S4mpp\Laraguard\Helpers\Device;
use S4mpp\Laraguard\Helpers\Layout;
use S4mpp\Laraguard\Tests\TestCase;

final class LayoutTest extends TestCase
{
    public function test_create_instance()
	{
		$layout = new Layout();

		$this->assertSame('laraguard::html', $layout->getHtmlFile());
		$this->assertSame('laraguard::auth.main', $layout->getAuthFile());
		$this->assertSame('laraguard::restricted-area', $layout->getBodyFile());
	}
}
