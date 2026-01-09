<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class TraceIdMiddleware
{
    public const ATTRIBUTE = 'trace_id';
    public const HEADER = 'X-Trace-Id';

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = $request->header(self::HEADER);

        if (! is_string($traceId) || ! Str::isUuid($traceId)) {
            $traceId = (string) Str::uuid();
        }

        $request->attributes->set(self::ATTRIBUTE, $traceId);

        $response = $next($request);
        $response->headers->set(self::HEADER, $traceId);

        return $response;
    }
}

