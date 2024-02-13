<?php

namespace S4mpp\Laraguard\Tests\Feature\Commands;

use S4mpp\Laraguard\Tests\TestCase;

final class CheckTest extends TestCase
{
    public function test_check(): void
    {
        $this->artisan('laraguard:check')
            ->expectsOutputToContain('Panel: Restricted area')
            ->expectsOutputToContain('Panel: My account')
            ->assertSuccessful();
    }
}
