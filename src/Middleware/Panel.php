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
        $panel = Laraguard::getCurrentPanelByRoute($request->route()->getAction('as'));

		if(!$panel)
		{
			abort(404);
		}

		$request->merge([
			'laraguard_panel' => $panel->getGuardName()
		]);

        return $next($request);
    }
}
