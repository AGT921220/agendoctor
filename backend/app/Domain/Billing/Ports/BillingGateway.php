<?php

namespace App\Domain\Billing\Ports;

use App\Domain\Billing\PlanKey;

interface BillingGateway
{
    /**
     * Crea un Checkout Session (subscription mode) y retorna URL.
     */
    public function createCheckoutSession(PlanKey $plan, ?string $stripeCustomerId, string $successUrl, string $cancelUrl): array;

    /**
     * Crea Billing Portal Session y retorna URL.
     */
    public function createBillingPortalSession(string $stripeCustomerId, string $returnUrl): array;

    /**
     * Verifica firma de webhook y retorna evento parseado.
     *
     * @return array{type:string, data:array<string,mixed>}
     */
    public function constructWebhookEvent(string $payload, string $signatureHeader): array;

    /**
     * Obtiene subscription por ID (para status y current_period_end).
     *
     * @return array{status:string|null,current_period_end:int|null}
     */
    public function fetchSubscription(string $stripeSubscriptionId): array;
}

