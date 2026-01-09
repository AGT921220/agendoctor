<?php

namespace App\Application\Billing\UseCases;

use App\Domain\Billing\PlanKey;
use App\Domain\Billing\Ports\BillingGateway;
use App\Domain\Billing\Ports\SubscriptionRepository;
use DateTimeImmutable;
use DateTimeZone;

final readonly class HandleStripeWebhookUseCase
{
    public function __construct(
        private BillingGateway $billing,
        private SubscriptionRepository $subs,
    ) {
    }

    public function execute(string $payload, string $signatureHeader): void
    {
        $event = $this->billing->constructWebhookEvent($payload, $signatureHeader);

        $type = $event['type'] ?? '';
        $obj = $event['data'] ?? [];

        if (! is_string($type)) {
            return;
        }

        if ($type === 'checkout.session.completed') {
            $subscriptionId = $obj['subscription'] ?? null;
            $customerId = $obj['customer'] ?? null;
            $planKey = $obj['metadata']['plan_key'] ?? null;

            if (! is_string($subscriptionId) || ! is_string($customerId) || ! is_string($planKey)) {
                return;
            }

            $plan = PlanKey::from($planKey);
            $sub = $this->subs->getOrCreate();

            if (! $sub['stripe_customer_id']) {
                $this->subs->setStripeCustomerId($sub['id'], $customerId);
            }

            $stripeSub = $this->billing->fetchSubscription($subscriptionId);
            $periodEnd = isset($stripeSub['current_period_end']) && is_int($stripeSub['current_period_end'])
                ? (new DateTimeImmutable('@'.$stripeSub['current_period_end']))->setTimezone(new DateTimeZone('UTC'))
                : null;

            $this->subs->setStripeSubscription(
                id: $sub['id'],
                plan: $plan,
                subscriptionId: $subscriptionId,
                status: isset($stripeSub['status']) && is_string($stripeSub['status']) ? $stripeSub['status'] : null,
                currentPeriodEnd: $periodEnd,
            );

            return;
        }

        if ($type === 'customer.subscription.updated' || $type === 'customer.subscription.deleted') {
            $subscriptionId = $obj['id'] ?? null;
            $status = $type === 'customer.subscription.deleted' ? 'canceled' : ($obj['status'] ?? null);
            $periodEndTs = $obj['current_period_end'] ?? null;

            if (! is_string($subscriptionId)) {
                return;
            }

            $periodEnd = is_int($periodEndTs)
                ? (new DateTimeImmutable('@'.$periodEndTs))->setTimezone(new DateTimeZone('UTC'))
                : null;

            $this->subs->updateStatusByStripeSubscriptionId(
                subscriptionId: $subscriptionId,
                status: is_string($status) ? $status : null,
                currentPeriodEnd: $periodEnd,
            );

            return;
        }

        if ($type === 'invoice.payment_failed') {
            $subscriptionId = $obj['subscription'] ?? null;
            if (! is_string($subscriptionId)) {
                return;
            }

            // Stripe usualmente pasa la suscripción a 'past_due' / 'unpaid' vía subscription.updated,
            // pero marcamos algo conservador aquí también.
            $this->subs->updateStatusByStripeSubscriptionId(
                subscriptionId: $subscriptionId,
                status: 'past_due',
                currentPeriodEnd: null,
            );
        }
    }
}

