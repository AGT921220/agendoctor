<?php

namespace App\Application\Billing\UseCases;

use App\Domain\Billing\Ports\BillingGateway;
use App\Domain\Billing\Ports\SubscriptionRepository;

final readonly class CreateBillingPortalSessionUseCase
{
    public function __construct(
        private SubscriptionRepository $subs,
        private BillingGateway $billing,
    ) {
    }

    /**
     * @return array{url:string}
     */
    public function execute(): array
    {
        $sub = $this->subs->getOrCreate();

        if (! $sub['stripe_customer_id']) {
            throw new \RuntimeException('No existe customer en Stripe.');
        }

        $frontend = rtrim((string) config('billing.frontend_url'), '/');
        $returnUrl = $frontend.'/billing';

        $session = $this->billing->createBillingPortalSession($sub['stripe_customer_id'], $returnUrl);

        return [
            'url' => (string) $session['url'],
        ];
    }
}

