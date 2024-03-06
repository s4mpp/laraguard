<?php

namespace S4mpp\Laraguard\Tests\Unit\Traits;

use Exception;
use S4mpp\Laraguard\Helpers\Utils;
use S4mpp\Laraguard\Base\Page;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;

final class TittleSluggableTest extends TestCase
{
    public function test_set_title()
    {
        $page = new Page('Title', 'title-slug');

        $this->assertSame('Title', $page->getTitle());
        $this->assertSame('title-slug', $page->getSlug());
    }

    public function test_set_slug()
    {
        $page = new Page('', '');

        $page->setSlug('new-title-slug');

        $this->assertSame('new-title-slug', $page->getSlug());
    }

    public function test_set_invalid_slug()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid slug regex (invalid slug)');

        $page = new Page('', 'old-slug');

        $page->setSlug('invalid slug');

        $this->assertSame('old-slug', $page->getSlug());
    }
}
