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
		$route = $request->route()->getAction('as');

		$panel = LaraguardBase::getPanel($request->get('laraguard_panel'));
		
		$module = $panel->getCurrentModuleByRoute($route);
		
		abort_if(!$module, 404);
		
		$page = $module->getCurrentPageByRoute($route);
		
		abort_if(!$page, 404);

		$request->merge([
			'laraguard_module' => $module->getSlug(),
			'laraguard_page' => $page->getSlug()
		]);

        return $next($request);
    }
}
