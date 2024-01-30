<?php

namespace S4mpp\Laraguard\Middleware;

use Closure;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard as LaraguardBase;
use Symfony\Component\HttpFoundation\Response;

class Page
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		$panel = LaraguardBase::getPanel($request->get('laraguard_panel'));

		$page = $panel->getCurrentPageByRoute($request->route()->getAction('as'));

		if(!$page)
		{
			abort(404);
		}

		$request->merge([
			'laraguard_page' => $page->getSlug()
		]);

        return $next($request);
    }
}
