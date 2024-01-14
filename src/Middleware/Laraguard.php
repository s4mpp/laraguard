<?php

namespace S4mpp\Laraguard\Middleware;

use Closure;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard as LaraguardBase;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Laraguard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard_name): Response
    {
        $guard = LaraguardBase::getGuard($guard_name);

        if(!Auth::guard($guard->getGuardName())->check())
        {
            return to_route($guard->getRouteName('login'))->withErrors('You are not logged in');
        }

        return $next($request);
    }
}
