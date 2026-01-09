<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Evita caché de usuarios/guards entre requests (útil para tests y runtimes persistentes).
 */
final class ResetAuthGuardsMiddleware
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        app('auth')->forgetGuards();

        return $next($request);
    }
}

