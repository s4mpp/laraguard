<?php

namespace S4mpp\Laraguard\Middleware;

use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Auth\Middleware\Authenticate;

/**
 * @codeCoverageIgnore
 */
final class RestrictedArea extends Authenticate
{
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : $this->getRoutePanel($request);
    }

    private function getRoutePanel($request)
    {
        $panel = Laraguard::getPanel(Panel::current());

        if ($panel) {

            $route_login = $panel->getRouteName('login');

            return route($route_login);
        }

        return null;
    }
}
