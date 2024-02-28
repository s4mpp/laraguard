<?php

namespace S4mpp\Laraguard\Tests\Unit\Helpers;

use S4mpp\Laraguard\Helpers\Utils;
use S4mpp\Laraguard\Helpers\Device;
use S4mpp\Laraguard\Tests\TestCase;

final class DeviceTest extends TestCase
{
    public function test_get_ip()
    {
        $ip = Device::ip();

        $this->assertNull($ip);
    }


    public function test_get_browser()
    {
        $browser = Device::browser();

        $this->assertNull($browser);
    }

    public function test_get_os()
    {
        $os = Device::os();

        $this->assertNull($os);
    }


    public function test_is_mobile()
    {
        $is_mobile = Device::isMobile();

        $this->assertFalse($is_mobile);
    }
}
