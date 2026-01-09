<?php

namespace App\Domain\Billing\Ports;

use App\Domain\Billing\PlanKey;

interface SubscriptionRepository
{
    /**
     * Retorna la suscripción singleton (creándola si no existe).
     *
     * @return array{
     *   id:int,
     *   status:string|null,
     *   plan_key:string|null,
     *   stripe_customer_id:string|null,
     *   stripe_subscription_id:string|null,
     *   current_period_end:string|null,
     *   limits_json:array<string,mixed>|null
     * }
     */
    public function getOrCreate(): array;

    public function setStripeCustomerId(int $id, string $customerId): void;

    public function setStripeSubscription(int $id, PlanKey $plan, string $subscriptionId, ?string $status, ?\DateTimeImmutable $currentPeriodEnd): void;

    public function updateStatusByStripeSubscriptionId(string $subscriptionId, ?string $status, ?\DateTimeImmutable $currentPeriodEnd): void;
}

