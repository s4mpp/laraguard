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
            'chrome 1' => ['Chrome', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'],
            'chrome 2' => ['Chrome', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'],
            'chrome 3' => ['Chrome', 'Linux', false, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'],
            'chrome 4' => ['Chrome', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/122.0.6261.89 Mobile/15E148 Safari/604.1'],
            'chrome 5' => ['Chrome', 'iPad', false, 'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/122.0.6261.89 Mobile/15E148 Safari/604.1'],
            'chrome 6' => ['Chrome', 'iPhone', true, 'Mozilla/5.0 (iPod; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/122.0.6261.89 Mobile/15E148 Safari/604.1'],
            'chrome 7' => ['Chrome', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36'],

            'firefox 1' => ['Firefox', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox 2' => ['Firefox', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14.3; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox 3' => ['Firefox', 'Linux', false, 'Mozilla/5.0 (X11; Linux i686; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox 4' => ['Firefox', 'Linux', false, 'Mozilla/5.0 (X11; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox 5' => ['Firefox', 'Ubuntu', false, 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox 6' => ['Firefox', 'Ubuntu', false, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox 7' => ['Firefox', 'Fedora', false, 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0'],
            'firefox 8' => ['Firefox', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/123.0 Mobile/15E148 Safari/605.1.15'],
            'firefox 9' => ['Firefox', 'iPad', false, 'Mozilla/5.0 (iPad; CPU OS 14_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/123.0 Mobile/15E148 Safari/605.1.15'],
            'firefox 10' => ['Firefox', 'iPhone', true, 'Mozilla/5.0 (iPod touch; CPU iPhone OS 14_3_1 like Mac OS X) AppleWebKit/604.5.6 (KHTML, like Gecko) FxiOS/123.0 Mobile/15E148 Safari/605.1.15'],

            'safari 11' => ['Safari', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_3_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3.1 Safari/605.1.15'],
            'safari 12' => ['Safari', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3.1 Mobile/15E148 Safari/604.1'],
            'safari 13' => ['Safari', 'iPad', false, 'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3.1 Mobile/15E148 Safari/604.1'],
            'safari 14' => ['Safari', 'iPhone', true, 'Mozilla/5.0 (iPod touch; CPU iPhone 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3.1 Mobile/15E148 Safari/604.1'],

            'ie 1' => ['Internet Explorer', 'Windows XP', false, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)'],
            'ie 2' => ['Internet Explorer', 'Windows Vista', false, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)'],
            'ie 3' => ['Internet Explorer', 'Windows 7', false, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)'],
            'ie 4' => ['Internet Explorer', 'Windows Vista', false, 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)'],
            'ie 5' => ['Internet Explorer', 'Windows 7', false, 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'],
            'ie 6' => ['Internet Explorer', 'Windows 7', false, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)'],
            'ie 7' => ['Internet Explorer', 'Windows 8', false, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)'],
            'ie 8' => ['Internet Explorer', 'Windows 7', false, 'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko'],
            'ie 9' => ['Internet Explorer', 'Windows 8', false, 'Mozilla/5.0 (Windows NT 6.2; Trident/7.0; rv:11.0) like Gecko'],
            'ie 10' => ['Internet Explorer', 'Windows 8.1', false, 'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko'],
            'ie 11' => ['Internet Explorer', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Trident/7.0; rv:11.0) like Gecko'],

            'edge 1' => ['Edge', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.2365.66'],
            'edge 2' => ['Edge', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.2365.66'],
            'edge 3' => ['Edge', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; HD1913) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 EdgA/122.0.2365.56'],
            'edge 4' => ['Edge', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 EdgA/122.0.2365.56'],
            'edge 5' => ['Edge', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; Pixel 3 XL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 EdgA/122.0.2365.56'],
            'edge 6' => ['Edge', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; ONEPLUS A6003) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 EdgA/122.0.2365.56'],
            'edge 7' => ['Edge', 'iPhone', true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 EdgiOS/122.2365.71 Mobile/15E148 Safari/605.1.15'],
            'edge 8' => ['Edge', 'Android', true, 'Mozilla/5.0 (Windows Mobile 10; Android 10.0; Microsoft; Lumia 950XL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36 Edge/40.15254.603'],
            'edge 9' => ['Edge', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; Xbox; Xbox One) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edge/44.18363.8131'],

            'opera 10' => ['Opera', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 OPR/108.0.0.0'],
            'opera 11' => ['Opera', 'Windows 10', false, 'Mozilla/5.0 (Windows NT 10.0; WOW64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 OPR/108.0.0.0'],
            'opera 12' => ['Opera', 'Mac OS X', false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_3_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 OPR/108.0.0.0'],
            'opera 13' => ['Opera', 'Linux', false, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 OPR/108.0.0.0'],
            'opera 14' => ['Opera', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 OPR/76.2.4027.73374'],
            'opera 15' => ['Opera', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; SM-G970F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 OPR/76.2.4027.73374'],
            'opera 16' => ['Opera', 'Android', true, 'Mozilla/5.0 (Linux; Android 10; SM-N975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.6261.105 Mobile Safari/537.36 OPR/76.2.4027.73374'],
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
