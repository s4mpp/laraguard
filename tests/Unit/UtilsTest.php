<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Utils;
use S4mpp\Laraguard\Tests\TestCase;

final class UtilsTest extends TestCase
{
    public function segmentRouteProvider()
    {
        return [
            ['lg.web', 1, 'web'],
            ['lg.web.about', 2, 'about'],
            ['lg.web.contact', 2, 'contact'],
            [null, 2, null],
        ];
    }

    public function translateProvider()
    {
        return [
            ['test.key', [], 'test.key'],
            ['laraguard::login.go_back', [], 'Go back'],
            ['laraguard::password.mail', [], 'TRANSLATION ARRAY: {"subject":"Reset Password","text":"You are receiving this email because we received a password reset request for your account.","action":"Reset password","expiration":"This password reset link will expire in :count minutes.","notice":"If you did not request a password reset, no further action is required."}'],
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

    /**
     * @dataProvider translateProvider
     */
    public function test_translate($key, $replace, $expected): void
    {
        $translated = Utils::translate($key, $replace);

        $this->assertEquals($expected, $translated);
    }
}
