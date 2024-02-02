<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Utils;
use S4mpp\Laraguard\Tests\TestCase;

class UtilsTest extends TestCase
{	
	public function test_get_segment_by_route_name()
	{
		$route_segment = Utils::getSegmentRouteName(1, 'lg.web');

		$this->assertEquals('web', $route_segment);
	}
}