<?php

namespace S4mpp\Laraguard\Tests\Unit\Helpers;

use Exception;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use S4mpp\Laraguard\Helpers\Utils;
use S4mpp\Laraguard\Tests\TestCase;

final class UtilsTest extends TestCase
{
    public static function segmentRouteProvider()
    {
        return [
            ['lg.web', 1, 'web'],
            ['lg.web.about', 2, 'about'],
            ['lg.web.contact', 2, 'contact'],
            [null, 2, null],
        ];
    }

    /**
     * @dataProvider segmentRouteProvider
     */
    public function test_get_segment_by_route_name(?string $route_name, int $segment_number, ?string $expected_result = null): void
    {
        $route_segment = Utils::getSegmentRouteName($segment_number, $route_name);

        $this->assertEquals($expected_result, $route_segment);
    }

    public function test_rate_limiter()
    {
        $this->expectException(RuntimeException::class);

        Utils::rateLimiter();
        Utils::rateLimiter();
        Utils::rateLimiter();
        
        Utils::rateLimiter(); // ->exception
    }
}
