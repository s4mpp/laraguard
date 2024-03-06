<?php

namespace S4mpp\Laraguard\Helpers;

use Illuminate\Support\Facades\App;
use S4mpp\Laraguard\Helpers\Device;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

abstract class Utils
{
    public static function getSegmentRouteName(int $path_step, ?string $current_route = null): ?string
    {
        if (! $current_route) {

            /** @var string $current_route */
            $current_route = Route::current()?->getAction('as') ?? '';
        }

        $path_steps = explode('.', $current_route);

        return $path_steps[$path_step] ?? null;
    }

    public static function rateLimiter(string $key = 'rl', int $max_attempts = 3): void
    {
        $ip = Request::ip() ?? 'x';

        $identifier = $key.':'.$ip;

        /** @var string $error_message */
        $error_message = __('laraguard::auth.tries_password_exceeded');

        throw_if(RateLimiter::tooManyAttempts($identifier, $max_attempts), $error_message);

        RateLimiter::hit($identifier);
    }
}
