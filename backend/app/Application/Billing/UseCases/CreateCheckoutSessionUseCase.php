<?php

namespace App\Application\Billing\UseCases;

use App\Domain\Billing\PlanKey;
use App\Domain\Billing\Ports\BillingGateway;
use App\Domain\Billing\Ports\SubscriptionRepository;

final readonly class CreateCheckoutSessionUseCase
{
    public function __construct(
        private SubscriptionRepository $subs,
        private BillingGateway $billing,
    ) {
    }

    /**
     * @return array{url:string}
     */
    public function execute(PlanKey $plan): array
    {
        $sub = $this->subs->getOrCreate();

        $frontend = rtrim((string) config('billing.frontend_url'), '/');
        $successUrl = $frontend.'/billing/success';
        $cancelUrl = $frontend.'/billing/cancel';

        $created = $this->billing->createCheckoutSession(
            plan: $plan,
            stripeCustomerId: $sub['stripe_customer_id'],
            successUrl: $successUrl,
            cancelUrl: $cancelUrl,
        );

        if (! $sub['stripe_customer_id'] && isset($created['customer_id'])) {
            $this->subs->setStripeCustomerId($sub['id'], (string) $created['customer_id']);
        }

        return [
            'url' => (string) $created['url'],
        ];
    }
}

