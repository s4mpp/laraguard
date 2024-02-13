<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;

final class LaraguardTest extends TestCase
{
    public function test_get_panels(): void
    {
        $all_panels = Laraguard::getPanels();

        $this->assertIsArray($all_panels);
        $this->assertCount(2, $all_panels);
    }

    public function test_create_panel(): void
    {
        $creation = Laraguard::panel('New panel', 'new-panel', 'guard');

        $this->assertInstanceOf(Panel::class, $creation);
        $this->assertCount(3, Laraguard::getPanels());

        $this->assertSame('New panel', $creation->getTitle());
        $this->assertSame('guard', $creation->getGuardName());
        $this->assertSame('new-panel', $creation->getPrefix());

        $this->assertFalse($creation->hasAutoRegister());
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_get_panel($guard, $prefix): void
    {
        $panel = Laraguard::getPanel($guard);

        $this->assertInstanceOf(Panel::class, $panel);

        $this->assertSame($guard, $panel->getGuardName());
        $this->assertSame($prefix, $panel->getPrefix());
    }

    public function test_render_layout(): void
    {
        $layout = Laraguard::layout();

        $this->assertNull($layout);
    }
}
