<?php

namespace S4mpp\Laraguard\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Concerns\WithWorkbench;

use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\workbench_path;
use Orchestra\Testbench\TestCase as BaseTestCase;
use S4mpp\Laraguard\Providers\LaraguardServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;
}
