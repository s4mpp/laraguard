<?php

namespace S4mpp\Laraguard\Tests\Unit\Navigation;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\{Auth, Hash};
use S4mpp\Laraguard\Navigation\{Breadcrumb, Menu, MenuItem};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class BreadcrumbTest extends TestCase
{
    public function test_create_breadcrumb(): void
    {
        $breadcrumb = new Breadcrumb('Title breacrumb', 'url-of-breadcrumb');

        $this->assertSame('Title breacrumb', $breadcrumb->getTitle());
        $this->assertSame('url-of-breadcrumb', $breadcrumb->getRoute());
    }
}
