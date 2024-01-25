<?php

namespace S4mpp\Laraguard\Middleware;

use Closure;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use Symfony\Component\HttpFoundation\Response;

class Panel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Laraguard::getCurrentGuardByRoute($request->route()->getAction('as'));

		if(!$guard)
		{
			abort(404);
		}

		$request->merge([
			'laraguard_panel' => $guard->getGuardName()
		]);

        return $next($request);
    }
}
