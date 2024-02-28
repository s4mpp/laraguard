<?php

namespace S4mpp\Laraguard;

use Illuminate\Support\Facades\App;
use S4mpp\Laraguard\Helpers\Device;
use Illuminate\Support\Facades\RateLimiter;

abstract class Utils
{
    public static function getSegmentRouteName(int $path_step, ?string $current_route = null): ?string
    {
        if (! $current_route) {
            $current_route = request()?->route()?->getAction('as');
        }

        $path_steps = explode('.', $current_route);

        return $path_steps[$path_step] ?? null;
    }

    public static function rateLimiter(string $key = 'rate-limiter'): void
    {
        $ip = Device::ip();

        $identifier = $key.':'.$ip;

        throw_if(! App::environment('testing') && RateLimiter::tooManyAttempts($identifier, 3), __('laraguard::auth.tries_password_exceeded'));

        RateLimiter::hit($identifier);
    }
}
