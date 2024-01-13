<?php

namespace S4mpp\Laraguard\Facades;

use Illuminate\Support\Facades\Facade;

class RoutesGuard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'routesguard';
    }
}