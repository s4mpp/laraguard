<?php

namespace S4mpp\Laraguard\Middleware;

use Closure;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictedArea
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Laraguard::getGuard($request->get('laraguard_panel'));

        if(!Auth::guard($guard->getGuardName())->check())
        {
            return to_route($guard->getRouteName('login'))->withErrors('You are not logged in');
        }

        return $next($request);
    }
}