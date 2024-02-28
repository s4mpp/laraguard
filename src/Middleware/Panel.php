<?php

namespace S4mpp\Laraguard\Middleware;

use Closure;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel as BasePanel;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
final class Panel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Laraguard::getPanel(BasePanel::current());

        $request->attributes->add([
            'laraguard_panel' => $panel,
        ]);

        return $next($request);
    }
}
