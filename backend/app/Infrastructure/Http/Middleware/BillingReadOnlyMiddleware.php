<?php

namespace App\Infrastructure\Http\Middleware;

use App\Domain\Billing\Ports\SubscriptionRepository;
use Closure;
use Illuminate\Http\Request;

/**
 * Bloquea operaciones de escritura si la suscripción no está active/trialing.
 * Se aplica SOLO a rutas mutables (POST/PATCH/PUT/DELETE de negocio).
 */
final class BillingReadOnlyMiddleware
{
    public function __construct(
        private readonly SubscriptionRepository $subs,
    ) {
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $sub = $this->subs->getOrCreate();
        $status = $sub['status'];

        $isActive = in_array($status, ['active', 'trialing'], true);

        if (! $isActive) {
            return response()->json([
                'message' => 'Suscripción inactiva. Modo solo lectura.',
                'errors' => (object) [],
                'trace_id' => (string) ($request->attributes->get(TraceIdMiddleware::ATTRIBUTE) ?? ''),
            ], 402);
        }

        return $next($request);
    }
}

