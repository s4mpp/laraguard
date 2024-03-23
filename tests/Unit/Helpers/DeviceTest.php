<?php

namespace S4mpp\Laraguard\Tests\Unit\Helpers;

use S4mpp\Laraguard\Helpers\Utils;
use S4mpp\Laraguard\Helpers\Device;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Request;

final class DeviceTest extends TestCase
{
    public static function userAgentProvider()
    {
        return [
            'chrome Windows 10' => ['Chrome', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'],
            'chrome Mac OS X' => ['Chrome', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'],
            'chrome Linux' => ['Chrome', 'Linux', false, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'],
            'chrome iPhone' => ['Chrome', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/122.0.6261.89 Mobile/15E148 Safari/604.1'],
            'chrome iPad' => ['Chrome', 'iPad', false, 'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/122.0.6261.89 Mobile/15E148 Safari/604.1'],
            'chrome Android' => ['Chrome', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36'],

            'firefox Windows 10' => ['Firefox', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox Mac OS X' => ['Firefox', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14.3; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox Linux' => ['Firefox', 'Linux', false, 'Mozilla/5.0 (X11; Linux i686; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox Linux' => ['Firefox', 'Linux', false, 'Mozilla/5.0 (X11; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox Ubuntu' => ['Firefox', 'Ubuntu', false, 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox Fedora' => ['Firefox', 'Fedora', false, 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox iPhone' => ['Firefox', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/123.0 Mobile/15E148 Safari/605.1.15'],
            'firefox iPad' => ['Firefox', 'iPad', false, 'Mozilla/5.0 (iPad; CPU OS 14_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/123.0 Mobile/15E148 Safari/605.1.15'],

            'safari Mac OS X' => ['Safari', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_3_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3.1 Safari/605.1.15'],
            'safari iPhone' => ['Safari', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3.1 Mobile/15E148 Safari/604.1'],
            'safari iPad' => ['Safari', 'iPad', false, 'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3.1 Mobile/15E148 Safari/604.1'],

            'ie Windows XP' => ['Internet Explorer', 'Windows XP', false, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)'],
            'ie Windows Vista' => ['Internet Explorer', 'Windows Vista', false, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)'],
            'ie Windows 7' => ['Internet Explorer', 'Windows 7', false, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)'],
            'ie Windows 8' => ['Internet Explorer', 'Windows 8', false, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)'],
            'ie Windows 7' => ['Internet Explorer', 'Windows 7', false, 'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko'],
            'ie Windows 8.1' => ['Internet Explorer', 'Windows 8.1', false, 'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko'],
            'ie Windows 10' => ['Internet Explorer', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Trident/7.0; rv:11.0) like Gecko'],

            'edge Windows 10' => ['Edge', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.2365.66'],
            'edge Mac OS X' => ['Edge', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.2365.66'],
            'edge Android' => ['Edge', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; HD1913) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 EdgA/122.0.2365.56'],
            'edge iPhone' => ['Edge', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 EdgiOS/122.2365.71 Mobile/15E148 Safari/605.1.15'],

            'opera Windows 10' => ['Opera', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 OPR/108.0.0.0'],
            'opera Mac OS X' => ['Opera', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_3_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 OPR/108.0.0.0'],
            'opera Linux' => ['Opera', 'Linux', false, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 OPR/108.0.0.0'],
            'opera Android' => ['Opera', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 OPR/76.2.4027.73374'],
        ];
    }

    /**
     * @dataProvider userAgentProvider
     */
    public function test_device(string $browser, string $os, bool $is_mobile, string $user_agent)
    {
        $test_get_browser = Device::browser($user_agent);
        $test_get_os = Device::os($user_agent);
        $test_get_is_mobile = Device::isMobile($user_agent);

        $this->assertSame($browser, $test_get_browser);
        $this->assertSame($os, $test_get_os);
        
        $this->assertIsBool($test_get_is_mobile);
        $this->assertSame($is_mobile, $test_get_is_mobile);
    }

    public function test_invalid_device()
    {
        $test_get_browser = Device::browser('xxxxxxx');
        $test_get_os = Device::os('xxxxxxx');
        $test_get_is_mobile = Device::isMobile('xxxxxxx');

        $this->assertNull($test_get_browser);
        $this->assertNull($test_get_os);
        $this->assertFalse($test_get_is_mobile);
    }
}
