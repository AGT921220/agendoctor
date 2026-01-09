<?php

namespace App\Infrastructure\Http\Middleware;

use App\Infrastructure\Tenant\RequestCurrentTenantProvider;
use Closure;
use Illuminate\Http\Request;

final class ResolveTenantFromAuthenticatedUser
{
    public function __construct(
        private readonly RequestCurrentTenantProvider $currentTenant,
    ) {
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $this->currentTenant->setTenantId($user ? (int) $user->tenant_id : null);

        return $next($request);
    }
}

